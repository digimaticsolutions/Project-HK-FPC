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
  * @package     Ced_CsSubAccount
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsSubAccount_Model_Observer
{
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{ 
		return Mage::getSingleton('customer/session');
	}
	
	/**
	 * Process vendor authentication
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Ced_CsSubAccount_Model_Observer
	 */
	public function subVendorAclCheck($observer)
	{
		$subVendor = $this->_getSession()->getSubVendorData();
		
		if(!Mage::getStoreConfig('ced_csmarketplace/general/cssubaccount',Mage::app()->getStore()->getId())) {
			return false;
		}
		if(!$this->_getSession()->getSubVendorData()){
			return false;
		}
		$pattern = implode(',', Ced_CsMarketplace_Controller_AbstractController::$openActions);
		
		$moduleName = Mage::app()->getRequest()->getModuleName();
		
		$controllerName = Mage::app()->getRequest()->getControllerName();
		$actionName = Mage::app()->getRequest()->getActionName();
		$resources = (array)Mage::getModel('cssubaccount/available')->getResourcesList();
		//print_r($resources);die;
		$current_link = $moduleName.'/'.$controllerName.'/'.$actionName;
		/* if($actionName=='index')
			$current_link = $moduleName.'/'.$controllerName; */
		//echo $current_link;
		if(!array_key_exists('vendor/'.$current_link,$resources) || strpos($pattern, $actionName)!==false || $subVendor['role']=='all'){ 
			//if($current_link=='csproduct/vproducts/index')
			return $this;
		}
		
		if($subVendor){

			$acl = explode(',', $subVendor['role']);
			//print_r($current_link);die;
			if(strpos($subVendor['role'], $current_link)!=true) {
				$count = explode('/',$acl[2]);
				$url = Mage::getUrl('csmarketplace/vendor/index',array('_secure'=>true));
				//echo $url;die;
				/* if(strpos($acl[1], 'csorder')==true && strpos($acl[1], '/index')!=true)
					$url = Mage::getUrl(ltrim($acl[1].'/index','vendor/'),array('_secure'=>true)); */
				//echo $url.'<br>';
				$observer->getEvent()->getAction()->getResponse()->setRedirect($url);
				$observer->getEvent()->getCurrent()->_allowedResource = false;
				
			} 
			
		}
		return $this;
		
	}
	
	/**
	 * Process Subvendor navigation
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Ced_CsSubAccount_Model_Observer
	 */
	public function subVendorNavigationLinksPrepare($observer)
	{
		
		if(!Mage::getStoreConfig('ced_csmarketplace/general/cssubaccount',Mage::app()->getStore()->getId())) {
			return false;
		}
		if(!$this->_getSession()->getSubVendorData()){
			return false;
		}
		
		$subVendor = $this->_getSession()->getSubVendorData();
		if($subVendor['role']=='all'){
			return $this;
		}
		if($subVendor){
			foreach($observer->getEvent()->getLinks() as $name=>$value) {
				$this->isAllowedResource($subVendor['role'],$observer,$value);
			}
		}
		return $this;
	}
	
	
	/**
	 * Checking resource allowed
	 *
	 * @param   Varien_Event_Observer $observer
	 * @return  Ced_CsSubAccount_Model_Observer
	 */
	public function isAllowedResource($acl,$observer,$link,$parent='')
	{ 
		
		/* echo $link->getPath();
		echo '<br>'; */
		
			
		//echo $link->getPath();
		$current ='';
		$current = rtrim($link->getPath(),'/');
		/* if(strpos($acl, 'csproduct')!==false ){ //die('klkjkl');
			if(strpos($current,'csmarketplace/vproducts')!==false)
				$current = str_replace('csmarketplace', 'csproduct', $current);
		} */
		
		if(strpos($current,'csmarketplace/vorders/index')!==false)
			$current = str_replace('/index', '', $current); 
		//echo $current.'<br>';
		if(!empty($current) && strpos($acl, $current)==false) { //echo $link->getName().'<br>';
			$observer->getEvent()->getBlock()->removeLink($link->getName(),$parent);
		} 
		else{
			if(count($link->getChildren())>0){
				foreach ($link->getChildren() as $key=>$child) {
					//echo $name;
					$name = strlen($parent) > 0?$parent.'~'.$link->getName():$link->getName();
					$this->isAllowedResource($acl,$observer,$child,$name);
				}
			}
		}
		
	}
}