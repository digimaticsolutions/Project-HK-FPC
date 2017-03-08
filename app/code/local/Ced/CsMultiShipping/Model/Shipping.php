<?php 
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMultiShipping
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMultiShipping_Model_Shipping extends Mage_Shipping_Model_Shipping{
    const SEPARATOR = '~';
    
    const METHOD_SEPARATOR = ':';
    
    /**
     * Multishipping Collect Rates
     *
     * @param $request
     * @return boolean
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request){
    	if(!Mage::helper('csmultishipping')->isEnabled()){	
    		return parent::collectRates($request);
    	}
    	$quotes=array();
    	$vendorActiveMethods =array(); 
    	$vendorAddressDetails=array();
    	foreach($request->getAllItems() as $item) {
    		if($vendorId = Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($item->getProduct())) {
    			$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
    			if($vendor && $vendor->getId()){
    				Mage::register('current_order_vendor',$vendor);
    			}		
    			if(isset($vendorActiveMethods[$vendorId]) && count($vendorActiveMethods[$vendorId])>0 
    					&& isset($vendorAddress[$vendorId]) && count($vendorAddress[$vendorId])>0){
    				$activeMethods = $vendorActiveMethods[$vendorId];
    				$vendorAddress = $vendorAddressDetails[$vendorId];
    			}
    			else{
    				$activeMethods=Mage::helper('csmultishipping')->getActiveVendorMethods($vendorId);
    				$vendorAddress=Mage::helper('csmultishipping')->getVendorAddress($vendorId);
    			}
				if(count($activeMethods)>0 && Mage::helper('csmultishipping')->validateAddress($vendorAddress)
				&& Mage::helper('csmultishipping')->validateSpecificMethods($activeMethods)){
					if(!isset($quotes[$vendorId])) 
						$quotes[$vendorId] = array();
					$quotes[$vendorId][] = $item;
					if(!isset($vendorActiveMethods[$vendorId]))
						$vendorActiveMethods[$vendorId]=$activeMethods;
					if(!isset($vendorAddressDetails[$vendorId]))
						$vendorAddressDetails[$vendorId]=$vendorAddress;
				} else {
					$quotes['admin'][] = $item;
				}
				if(Mage::registry('current_order_vendor')!=null) {
					Mage::unregister('current_order_vendor');
				}
    		}
    		else 
    			$quotes['admin'][] = $item;
    	}  
    	if(Mage::registry('current_order_vendor')!=null) {
    		Mage::unregister('current_order_vendor');
    	}
    	
    	$origRequest=clone $request;
		$last_count = 0;
		$prod_model = Mage::getModel('catalog/product');
		if(Mage::getSingleton('checkout/session')->getInvalidItem())
			Mage::getSingleton('checkout/session')->unsInvalidItem();
    	foreach ($quotes as $vendorId=>$items){
    		$request = clone $origRequest;
    		$request->setVendorId($vendorId);
  			$request->setVendorItems($items);
  			$request->setAllItems($items);
  			$request->setPackageWeight($this->getItemWeight($request, $items));
  			$request->setPackageQty($this->getItemQty($request, $items));
			$request->setPackageValue($this->getItemSubtotal($request, $items));
			$request->setBaseSubtotalInclTax($this->getItemSubtotal($request, $items));
    		$vendorcarriers=array();
    		if($vendorId!='admin'){
    			$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
    			if($vendor && $vendor->getId()){
    				Mage::register('current_order_vendor',$vendor);
    			}
    			$vendorCarriers = array_keys($vendorActiveMethods[$vendorId]);
    			$vendorAddress = array();
    			$vendorAddress = $vendorAddressDetails[$vendorId];
    			if(isset($vendorAddress['country_id']))
    				$request->setOrigCountry($vendorAddress['country_id']);
    			if(isset($vendorAddress['region']))
    				$request->setOrigRegionCode($vendorAddress['region']);
    			if(isset($vendorAddress['region_id'])){
    				$origRegionCode = $vendorAddress['region_id']; 
    				if (is_numeric($origRegionCode)) {
    					$origRegionCode = Mage::getModel('directory/region')->load($origRegionCode)->getCode();
    				}
    				$request->setOrigRegionCode($origRegionCode);
    			}
    			if(isset($vendorAddress['postcode']))
	    			$request->setOrigPostcode($vendorAddress['postcode']);
    			if(isset($vendorAddress['city']))
	    			$request->setOrigCity($vendorAddress['city']);
    		}
    			
	    	$storeId = $request->getStoreId();
	    	if (!$request->getOrig()) {
	    		$request
	    		->setCountryId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID, $storeId))
	    		->setRegionId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID, $storeId))
	    		->setCity(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_CITY, $storeId))
	    		->setPostcode(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE, $storeId));
	    	}
	    	
	    	$limitCarrier = $request->getLimitCarrier();
			
	    	if (!$limitCarrier) {
	    		$carriers = Mage::getStoreConfig('carriers', $storeId);
				
	    		foreach ($carriers as $carrierCode => $carrierConfig) {
	    			if($vendorId!='admin'){
	    				if(!in_array($carrierCode,$vendorCarriers))
	    					continue;
	    				$request->setVendorShippingSpecifics($vendorActiveMethods[$vendorId][$carrierCode]);
	    			}
	    			$this->collectCarrierRates($carrierCode, $request);
	    		}
	    	} else {
	    		if (!is_array($limitCarrier)) {
	    			$limitCarrier = array($limitCarrier);
	    		}
	    		foreach ($limitCarrier as $carrierCode) {
	    			if($vendorId!='admin'){
	    				if(!in_array($carrierCode,$vendorCarriers))
	    					continue;
	    			}
	    			$carrierConfig = Mage::getStoreConfig('carriers/' . $carrierCode, $storeId);
	    			if (!$carrierConfig) {
	    				continue;
	    			}
	    			$this->collectCarrierRates($carrierCode, $request);
	    		}	    		
	    	}
	    	if(Mage::registry('current_order_vendor')!=null) {
	    		Mage::unregister('current_order_vendor');
	    	}
			
			$total_count = count($this->getResult()->getAllRates());
			$current_count = $total_count - $last_count;
			$last_count = $total_count;
			if($current_count < 1){
				$prod_name = array();
				$prod_name = Mage::getSingleton('checkout/session')->getInvalidItem();
				foreach ($items as $item) {
					$prod_name[] = $prod_model->load($item->getProductId())->getName();
				}
				Mage::getSingleton('checkout/session')->setInvalidItem($prod_name);			
			}
			
    	}

        if((Mage::app()->getRequest()->getControllerName()=="cart")||!Mage::getSingleton('checkout/session')->getQuote()->getIsMultiShipping()){ 
	        $shippingRates = $this->getResult()->getAllRates();
	        $newVendorRates = array();
	        foreach ($this->ratesByVendor($shippingRates) as $vendorId=>$rates) {
	            if(!sizeof($newVendorRates)){
	                foreach($rates as $rate){
	                    $newVendorRates[$rate->getCarrier().'_'.$rate->getMethod()] = $rate->getPrice();
	                }
	            }else{
	                $tmpRates = array();
	                foreach($rates as $rate){
	                    foreach($newVendorRates as $code=>$shippingPrice){
	                        $tmpRates[$code.self::METHOD_SEPARATOR.$rate->getCarrier().'_'.$rate->getMethod()] = $shippingPrice+$rate->getPrice();
	                    }
	                }
	                $newVendorRates = $tmpRates;
	            }
	        }
	        foreach($newVendorRates as $code=>$shippingPrice){
	            $method = Mage::getModel('shipping/rate_result_method');
	            $method->setCarrier('vendor_rates');
	            $carrier_title = Mage::getStoreConfig('ced_csmultishipping/general/carrier_title',Mage::app()->getStore()->getId());
	            $method->setCarrierTitle(Mage::helper('csmultishipping')->__($carrier_title));
	             
	            $method->setMethod($code);
	            $carrier_title = Mage::getStoreConfig('ced_csmultishipping/general/method_title',Mage::app()->getStore()->getId());
	            $method->setMethodTitle(Mage::helper('csmultishipping')->__($carrier_title));
	             
	            $method->setPrice($shippingPrice);
	            $method->setCost($shippingPrice);
	            $this->getResult()->append($method);
	        }
        }
        return $this;
    }
    
    /**
     * Group shipping rates by each vendor.
     * @param unknown $shippingRates
     */
    public function ratesByVendor($shippingRates){
        $rates = array();
        foreach($shippingRates as $rate){
            if(!$rate->getVendorId())
            	$rate->setVendorId("admin");
            if(!isset($rates[$rate->getVendorId()])){
                $rates[$rate->getVendorId()] = array();
            }
            $rates[$rate->getVendorId()][] = $rate;
        }
        ksort($rates);
        return $rates;
    }
    
    /**
     * Retrieve item quantity by id
     *
     * @param int $itemId
     * @return float|int
     */
    public function getItemQty($request,$items)
    {
    	$qty = 0;
    	foreach ($items as $item) {
   			$qty += $item->getQty();
   		}
    	return $qty;
    }
    
    /**
     * Retrieve item quantity by id
     *
     * @param int $itemId
     * @return float|int
     */
    public function getItemWeight($request,$items)
    {
    	$qty = 0;
    	foreach ($items as $item) {
    		$qty += $item->getQty()*$item->getWeight();
    	}
    	return $qty;
    }
	
	 
	/**
     * Retrieve item Base subtotal by id
     *
     * @param int $itemId
     * @return float|int
     */
    public function getItemSubtotal($request,$items)
    {
    	$row_total = 0;
    	foreach ($items as $item) {
			$row_total += $item->getBaseRowTotalInclTax();
   		}
    	return $row_total;
    }
}