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
class Ced_CsMultiShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    
	/**
	 * Check module is enabled
	 *
	 * @param $storeId
	 * @return int
	 */
    public function isEnabled($storeId=0){
    	if($storeId==0)
    		$storeId=Mage::app()->getStore()->getId();
    	return Mage::getStoreConfig('ced_csmultishipping/general/activation', $storeId);
    }
    
    /**
     * Get Config Value
     *
     * @param $key, $vendorId
     * @return string|int
     */
    public function getConfigValue($key='',$vendorId=0){
    	$value=false;
    	if(strlen($key)>0 && $vendorId){
			$key_tmp=Mage::helper('csmarketplace')->getTableKey('key');
			$vendor_id_tmp=Mage::helper('csmarketplace')->getTableKey('vendor_id');
    		$vsettings = Mage::getModel('csmarketplace/vsettings')->loadByField(array($key_tmp,$vendor_id_tmp),array($key,(int)$vendorId));
    		if($vsettings && $vsettings->getSettingId())
    			$value = $vsettings->getValue();
    	}
    	return $value;
    }
    
    /**
     * Get Active Vendor methods
     *
     * @param $vendorId
     * @return array
     */
    public function getActiveVendorMethods($vendorId=0){
    	$methods = Mage::getModel('csmultishipping/source_shipping_methods')->getMethods();
    	$VendorMethods=array();
    	if(count($methods) >0 ) {
    		$vendorShippingConfig = $this->getShippingConfig($vendorId);
    		foreach($methods as $code=>$method) {
    			$model=Mage::getModel($method['model']);
    			$key = strtolower(Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract::SHIPPING_SECTION.'/'.$code.'/active');
    			if(isset($vendorShippingConfig[$key]['value']) && $vendorShippingConfig[$key]['value']){
	    			$fields = $model->getFields();
	    			if (count($fields) > 0) {
	    				foreach ($fields as $id=>$field) {
		    				$key = strtolower(Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract::SHIPPING_SECTION.'/'.$code.'/'.$id);
		    				if(isset($vendorShippingConfig[$key]))
		    					$VendorMethods[$code][$id] = $vendorShippingConfig[$key]['value'];
	    				}
	    			}
    			}
    		}
    		return $VendorMethods;
    	}
    	else 
    		return $VendorMethods;
    }
    
    /**
     * Get Vendor methods
     *
     * @param $vendorId
     * @return array
     */
    public function getVendorMethods($vendorId=0){
    	$methods = Mage::getModel('csmultishipping/source_shipping_methods')->getMethods();
    	$VendorMethods=array();
    	if(count($methods) >0 ) {
    		$vendorShippingConfig = $this->getShippingConfig($vendorId);
    		foreach($methods as $code=>$method) {
    			$model=Mage::getModel($method['model']);
    			$fields = $model->getFields();
    			if (count($fields) > 0) {
    				foreach ($fields as $id=>$field) {
    					$key = strtolower(Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract::SHIPPING_SECTION.'/'.$code.'/'.$id);
    					if(isset($vendorShippingConfig[$key]))
    						$VendorMethods[$code][$id] = $vendorShippingConfig[$key]['value'];
    				}
    			}
    		}
    		return $VendorMethods;
    	}
    	else
    		return $VendorMethods;
    }
    
    /**
     * Get Vendor Address
     *
     * @param $vendorId
     * @return array
     */
    public function getVendorAddress($vendorId=0){
    	$VendorAddress=array();
    	$model= Mage::getModel('csmultishipping/vsettings_shipping_address');
   		$vendorShippingConfig = $this->getShippingConfig($vendorId);
   		$fields = $model->getFields();
    	if (count($fields) > 0) {
    		foreach ($fields as $id=>$field) {
    			$key = strtolower(Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract::SHIPPING_SECTION.'/address/'.$id);
    			if(isset($vendorShippingConfig[$key]) && strlen($vendorShippingConfig[$key]['value'])>0)
    				$VendorAddress[$id] = $vendorShippingConfig[$key]['value'];
    		}
    	}
    	return $VendorAddress;
    }
    
    /**
     * Get Shipping Configuration
     *
     * @param $vendorId
     * @return array
     */
    public function getShippingConfig($vendorId=0){
    	$values=array();
    	if($vendorId){
			
			$group=Mage::helper('csmarketplace')->getTableKey('group');
			$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
    		$vsettings = Mage::getModel('csmarketplace/vsettings')
			    		->getCollection()
			    		->addFieldToFilter($group,array('eq'=>Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract::SHIPPING_SECTION))
			    		->addFieldToFilter($vendor_id,array('eq'=>$vendorId));
    		if($vsettings && count($vsettings->getData())>0){
    			foreach($vsettings->getData() as $index => $value){
    				$values[$value['key']]=$value;
    			}
    		}
    	}
    	return $values;
    }
    
    
    /**
     * Save shipping Data
     *
     * @param $section, $groups, $vendor_id
     * @return void
     */
    public function saveShippingData($section, $groups, $vendor_id){
    	foreach ($groups as $code=>$values) {
    		if(is_array($values) && count($values)>0){
	    		foreach ($values as $name=>$value) {
	    			$serialized = 0;
	    			$key = strtolower($section.'/'.$code.'/'.$name);
	    			if (is_array($value)){
	    				$value=implode(",", $value);
	    				//$value = serialize($value);
	    				//$serialized = 1;
	    			}
					$key_tmp=Mage::helper('csmarketplace')->getTableKey('key');
					$vendor_id_tmp=Mage::helper('csmarketplace')->getTableKey('vendor_id');
	    			$setting = Mage::getModel('csmarketplace/vsettings')->loadByField(array($key_tmp,$vendor_id_tmp),array($key,$vendor_id));
	    			if ($setting && $setting->getId()) {
	    				$setting->setVendorId($vendor_id)
	    				->setGroup($section)
	    				->setKey($key)
	    				->setValue($value)
	    				->setSerialized($serialized)
	    				->save();
	    			} else {
	    				$setting = Mage::getModel('csmarketplace/vsettings');
	    				$setting->setVendorId($vendor_id)
	    				->setGroup($section)
	    				->setKey($key)
	    				->setValue($value)
	    				->setSerialized($serialized)
	    				->save();
	    			}
	    		}
    		}
    	}
    }
    
    /**
     * Validate Address
     *
     * @param $vendorAddress
     * @return boolean
     */
    public function validateAddress($vendorAddress=array()){
    	$flag=true;
    	if(!isset($vendorAddress['country_id']) || !isset($vendorAddress['city']) || !isset($vendorAddress['postcode']))
    		return false;
    	if(!isset($vendorAddress['region_id']) &&!isset($vendorAddress['region']))
    		return false;
    	if(isset($vendorAddress['country_id']) && !$vendorAddress['country_id'])
    		return false;
    	if(isset($vendorAddress['region_id']) && !$vendorAddress['region_id'])
    		return false;
    	if(isset($vendorAddress['region']) && !$vendorAddress['region'])
    		return false;
    	if(!isset($vendorAddress['city']) && !$vendorAddress['city'])
    		return false;
    	if(!isset($vendorAddress['postcode']) && !$vendorAddress['postcode'])
    		return false;
    	return $flag;
    }
    
    /**
     * Validate Specific Methods
     *
     * @param $activeMethods
     * @return boolean
     */
    public function validateSpecificMethods($activeMethods){
    	if(count($activeMethods)>0){
    		$methods=Mage::getModel('csmultishipping/source_shipping_methods')->getMethods();
    		foreach ($activeMethods as $method => $methoddata){
    			if(isset($methods[$method]['model'])){
    				$model = Mage::getModel($methods[$method]['model'])->validateSpecificMethod($activeMethods[$method]);
    				if(!$model)
    					return false;
    			}			
    		}
    		return true;
    	}
    	else
    		return false;
    }
}