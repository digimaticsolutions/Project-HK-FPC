<?php
//require_once('Ced/CsMarketplace/controllers/VendorController.php');

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
  * @author   CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */



class Ced_CsSubAccount_VendorController extends Ced_CsMarketplace_Controller_AbstractController
{
	/* public function createAction()
	{
		if ($this->_getSession()->isLoggedIn()) {
			$this->_redirect('csmarketplace/vendor/index');
			return;
		}
	
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	} */
	
	/**
	 * Action vendor approval page
	 *
	 * Display vendor status and show a link for send request to admin (formally admin) for approval.
	 */
	public function approvalAction() 
	{
		if(!Mage::getSingleton("customer/session")->getParentVendor())
			return parent::approvalAction();
		
		if(!Mage::getStoreConfig('ced_csmarketplace/general/cssubaccount',Mage::app()->getStore()->getId())) {
			$this->_redirect('customer/account/');
			return;
		}
		
		
		$collection = Mage::getModel('cssubaccount/cssubaccount')->load($this->_getSession()->getCustomerEmail(),'email');
		if(!count($collection)){
			Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__('Customer does not exist'));
			$this->_redirect('csmarketplace/vendor/login/');
		}
		/* if($collection->getStatus()==1){
			
		} */
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Approval Status'));
		$this->renderLayout();
		//print_r($collection->getData());die('approval');
	
	}
	
}