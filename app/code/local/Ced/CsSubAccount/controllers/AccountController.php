<?php
require_once('Ced/CsMarketplace/controllers/AccountController.php');

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



class Ced_CsSubAccount_AccountController extends Ced_CsMarketplace_AccountController
{
	
	/**
     * Create customer account action
     */
    public function loginPostAction()
    { 
	    if (!$this->_validateFormKey()) {
	    	$this->_redirect('*/*/');
	    	return;
	    }
	    
	    if ($this->_getSession()->isLoggedIn()) {
	    	$this->_redirect('csmarketplace/vendor/index');
	    	return;
	    }
	    
	    $session = $this->_getSession();
	    if ($this->getRequest()->isPost()) {
	    	$login = $this->getRequest()->getPost('login');
	    	
	    	if (!empty($login['username']) && !empty($login['password'])) {
	    		try {
	    			/* To Support Vendor Seo Suite Addon */
	    			/* Start */
	    			if(Mage::getConfig()->getModuleConfig('Ced_CsSeoSuite')->is('active', 'true')){
	    				$helper = Mage::helper('csseosuite');
	    				if(is_object($helper) && $helper->isEnabled() && Mage::getStoreConfig('ced_csmarketplace/general/use_in_vendorpanel') && strlen($helper->getCustomUrl()) > 0) {
	    					$customer = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('email')->addAttributeToFilter('email',$login['username']);
	    					if(count($customer) > 0){
	    						$customer = $customer->getFirstItem();
	    						if($customer && $customer->getId()) {
	    							$customerSharedWebsiteIds = $customer->getSharedWebsiteIds();
	    							if(isset($customerSharedWebsiteIds[0])){
	    								$defaultStore = Mage::getModel('core/store')->load(Ced_CsSeoSuite_Helper_Data::VENDOR_PANEL_STORE_VIEW_CODE.'_'.$customerSharedWebsiteIds[0]);
	    								if($defaultStore && $defaultStore->getId()){
	    									Mage::app()->setCurrentStore($defaultStore->getId());
	    								}
	    							}
	    						}
	    					}
	    				}
	    			}
	    			/* End */
	    			
	    			// Validate the sub Vendor Account
	    			
	    			$check = Mage::getModel('csmarketplace/vendor')->loadByEmail($login['username']);
	    			if($check && $check->getId()){
	    				$session->login($login['username'], $login['password']);
	    				if ($session->getCustomer()->getIsJustConfirmed()) {
	    					$this->_welcomeCustomer($session->getCustomer(), true);
	    				}
	    			} else {
	    				$subVendors = Mage::getModel('cssubaccount/cssubaccount')->load($login['username'],'email')->getData();
	    				if(!empty($subVendors) && $subVendors['status']==1)
	    				{
	    					
	    					if(Mage::helper('core')->decrypt($subVendors['password'])==$login['password']){
	    						$vendor = Mage::getModel('csmarketplace/vendor')->load($subVendors['parent_vendor']);
	    						$customer = Mage::getModel('customer/customer')->load($vendor->getCustomerId());
	    						$session->setCustomerAsLoggedIn($customer);
	    						$session->setSubVendorData($subVendors);
	    						
	    					}
	    				}
	    			}
	    			
	    			
	    			
	    		} catch (Mage_Core_Exception $e) {
	    			switch ($e->getCode()) {
	    				case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
	    					$value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
	    					$message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
	    					break;
	    				case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
	    					$message = $e->getMessage();
	    					break;
	    				default:
	    					$message = $e->getMessage();
	    			}
	    			$session->addError($message);
	    			$session->setUsername($login['username']);
	    		} catch (Exception $e) {
	    			// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
	    		}
	    	} else {
	    		$session->addError($this->__('Login and password are required.'));
	    	}
	    }
	    $this->_loginPostRedirect();
	    	//$customer = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
	    	//$authentic = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->authenticate($email, $password);
    }
}