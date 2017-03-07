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
  * @package     Ced_CsVendorProductAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Observer model
 *
 * @category    Ced
 * @package     Ced_CsVendorProductAttribute
 */
Class Ced_CsVendorProductAttribute_Model_Observer
{
	/**
	 * Get Customer Session
	 */
	protected function _getSession() {
		return Mage::getSingleton('customer/session');
	}
	
	/**
	 * Create Attribute Set
	 */
	public function createattributeset($observer) {
		
		//Check the customer is vendor or not
	
		$customer_data=$observer->getEvent()->getCustomer();
		$cust_email=$customer_data->getEmail();
		$Check_vendor= Mage::getModel('csmarketplace/vendor')->loadByCustomerId($this->_getSession()->getCustomerId());
		if($Check_vendor){
		$vendor_id=$Check_vendor->getId();
		$vendor_shop_url=$Check_vendor->getShopUrl();
	
        if($vendor_id){
        
		    /**
		     * Check If exist or not
		     */
			$vendor_attribute_set='vendor_'.$vendor_shop_url;
			$sets = Mage::getModel("eav/entity_attribute_set")->getCollection()->getData();
			foreach ($sets as $key=>$value){
			   $array_sets[]=$value['attribute_set_name'] ;
			}
		
			if (in_array($vendor_attribute_set, $array_sets)) {
			
			//Mage::getSingleton('customer/session')->addSuccess('Your already have Your Attribute set');
			}
			else{
			/**
			 * Create Attribute Set For vendor If Not Exist
			 */
			$skeletonID = Mage::getModel('eav/entity')
			->setType('catalog_product')
			->getTypeId(); //Default 
			$entityTypeId = Mage::getModel('catalog/product')
			->getResource()
			->getEntityType()
			->getId();
			$attributeSet = Mage::getModel('eav/entity_attribute_set')
			->setEntityTypeId($entityTypeId)
			->setAttributeSetName($vendor_attribute_set);
			
			$attributeSet->validate();
			$attributeSet->save();
			$attributeSet->initFromSkeleton($skeletonID)->save();
			
			/**
			 * Save the attribute set along with vendor
			 */
			$attribute_set= Mage::getModel('csvendorproductattribute/attributeset');
			$attribute_set->setVendorId($vendor_id);
			$attribute_set->setAttributeSet($vendor_attribute_set);
			$attribute_set->save();
		    Mage::getSingleton('customer/session')->addSuccess('Your Unique attribute set has been created');
			}
          }
		}
	 }
}