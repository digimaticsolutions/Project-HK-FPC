<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsStorePickup
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsStorePickup_Model_Carrier_Storepickup extends Ced_StorePickup_Model_Carrier_Storepickup
{
	protected $_carriercode = "ced_storepickup";
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		try{
		if(!Mage::helper('csmultishipping')->isEnabled())
		{
			return parent::collectRates($request);
		}
		if(!Mage::helper('csstorepickup')->isEnabled())
		{
			return parent::collectRates($request);
		}
		$result = Mage::getModel('shipping/rate_result');
		$vendorId = $request->getVendorId();
		$destcountry = $request->getDestCountryId();
		
		$destRegion =  $request->getDestRegionCode();
		$destStreet = $request->getDestStreet();
		$destcity = $request->getDestCity();
		$destpostcode = $request->getDestPostcode();
		
		if($vendorId!="admin")
		{
			
			$vendorSpecificConfig = $request->getVendorShippingSpecifics();
			$availableCountries =explode(',',$vendorSpecificConfig['allowed_country']);
			$allcountry = false;
			$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		
			if(!in_array($destcountry, $availableCountries))
			{
				return false;
			}
			
			$storesCollection = Mage::getModel('storepickup/storepickup')->getCollection()
			->addFieldToFilter('is_approved','1')->addFieldToFilter('vendor_id',array('in'=>$vendorId));
		    $count=0;$i=0;
			foreach($storesCollection as $stores) {
				$i++;
				if($stores['store_country'] == $destcountry )
				{
			
					$countryIds[] = $stores->getStoreCountry();
					$regionCodes[] = $stores->getStoreState();
					$address[] = $stores['store_address'].', '.$stores['store_city'].', '.$stores['store_state'].', '.$stores['store_country'];
					
					$custom_method = $this->_carriercode.$i.Ced_CsMultiShipping_Model_Shipping::SEPARATOR.$vendor->getId();
					$method = Mage::getModel('shipping/rate_result_method');
					$method->setCarrier($this->_carriercode);
					$method->setVendorId($vendor->getId());
					$method->setCarrierTitle($address[$count]);
					$method->setMethod($custom_method);
					$method->setMethodTitle($address[$count]);
					$method->setPrice($stores['shipping_price']);
					$method->setCost(0);
					$result->append($method);
					$count++;
				}
			}
			
			return $result;
		}
		else{
			return parent::collectRates($request);
		}
	}
	catch(Exception $e)
	{
		die($e->getMessage());
	}
	}
	
}