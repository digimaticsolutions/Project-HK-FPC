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

class Ced_CsVendorProductAttribute_Model_System_Config_Source_Vproducts_Set extends Ced_CsMarketplace_Model_System_Config_Source_Vproducts_Set
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($defaultValues = false,$withEmpty = false)
    {
		$options = array();
		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
					->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
					->load()
					->toOptionHash();
		 /**
		 * Array of custom attribute set
		 */
		$attribute_set= Mage::getModel('csvendorproductattribute/attributeset')->getCollection()->getData();
		$array_set=array();
		foreach ($attribute_set as $key=>$value){
			$array_set[]=$value['attribute_set'];
			}
			
		if (!$defaultValues)
			$allowedSet = $this->getAllowedSet(Mage::app()->getStore()->getId());
		
		foreach($sets as $value=>$label) {
			/**
			 * Skip The Vendor custom attribute sets for admin
			 */
			if(!Mage::getSingleton('customer/session')->getVendorId()){
				if(in_array($label,$array_set))continue;
			}
			if(!$defaultValues && !in_array($value,$allowedSet)) continue;
			$options[] = array('value'=>$value,'label'=>$label);
			
		}
	
		
		if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
      
		return $options;
    }
	
	/**
	 * Get Allowed product attribute set
	 *
	 */
	public function getAllowedSet($storeId = 0) {
		
		$vendor_id=Mage::getSingleton('customer/session')->getVendorId();
		$csmarket_vendor=Mage::getModel('csmarketplace/vendor')->load($vendor_id);
		$vendor_code=$csmarket_vendor->getShop_url();
		$attribute_set_name='vendor_'.$vendor_code;
		$setupmodel = Mage::getModel('eav/entity_setup','core_setup');
		$attributeSetId=$setupmodel->getAttributeSetId('catalog_product',$attribute_set_name);
		$allowed_sets_store=Mage::getStoreConfig('ced_vproducts/general/set',$storeId) . "," . $attributeSetId;
		$allowed_sets=Mage::getStoreConfig('ced_vproducts/general/set') . "," . $attributeSetId;
		
		if($storeId) return explode(',',$allowed_sets_store);
	    return explode(',',$allowed_sets);
	}

}