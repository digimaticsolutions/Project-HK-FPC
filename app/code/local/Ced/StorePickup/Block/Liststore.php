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
 * @package     Ced_StorePickup
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_StorePickup_Block_Liststore extends Mage_Core_Block_Template {
	
	/* public function _construct()
	{
		die("kjtg");
	} */


	public function getAllStores() {
		$data = $this->getRequest()->getPost();
		//print_r($data);die("lknjlk");
		$country = '';
        $state = '';
        $city = '';
		if(isset($data['country_id'])){
			$country = trim($data['country_id']);
			
		}
		
		if(isset($data['region_id'])){
			$state = trim($data['region_id']);
		}

		if(isset($data['city'])){
			$city = trim($data['city']);
		}
		
		$collection = Mage::getModel('storepickup/storepickup')->getCollection()
						   ->addFieldToFilter('is_active','1');
		
		if($country){
			 $collection->addFieldToFilter('store_country', array('like'=>$country));
		}
		
		if($state){
			$collection->addFieldToFilter('store_state', array('like'=>$state));
		}
		
		if($city){
			$collection->addFieldToFilter('store_city', array('like'=>$city));
		}
	
        return $collection;
	}
	
	public function getFullRouteInfo() {
		return $this->getRequest()->getActionName();
	}
	
	public function getFullCon() {
		$contry = Mage::getModel('adminhtml/system_config_source_country') ->toOptionArray();
		
		return Mage::getModel('adminhtml/system_config_source_country') ->toOptionArray();
	}
    
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
  
}