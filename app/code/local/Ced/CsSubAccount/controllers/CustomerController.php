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
  * @author   CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */



class Ced_CsSubAccount_CustomerController extends Ced_CsMarketplace_Controller_AbstractController
{
	/**
	 * Grid action
	 */
	public function gridAction()
	{
		if(!$this->_getSession()->getVendorId())
			$this->_redirect('csmarketplace/account/login/');
		$this->loadLayout();
		$this->renderLayout();
	}
	
	protected function _getSession() 
	{
		return Mage::getSingleton ( 'customer/session' );
	}
	
	/* Function to list associated customers */
	
	public function listAction()
	{
		if(!$this->_getSession()->getVendorId())
			$this->_redirect('csmarketplace/account/login/');

		$this->loadLayout();
		$this->renderLayout();
	
	}
	
	
	public function saveResourceAction()
	{
		
		if(!$this->_getSession()->getVendorId())
			$this->_redirect('csmarketplace/account/login/');
		
		$id = $this->getRequest()->getPost('subVendorId');
		try{
			if($this->getRequest()->getPost('all')==1){
				$model = Mage::getModel('cssubaccount/cssubaccount')->load($id)->setRole('all');
				$model->save();
			}
			else{
				$resources = $this->getRequest()->getPost('resource');
				//print_r($resources);die;
				if(strpos($resources, '__root__') !== false){ 
					$resources = str_replace('__root__,','',$resources);
				}
				$model = Mage::getModel('cssubaccount/cssubaccount')->load($id)->setRole($resources);
				$model->save();
				$msg = 'Resources are successfully alloted to the Sub-Vendor';	
			}
		}catch(Exception $e){
			$msg = $e->getMessage();
			/* Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__($msg));
			$this->_redirect('cssubaccount/customer/list/');
			return; */
		}
		
		$this->_getSession()->addSuccess(Mage::helper ('csmarketplace')->__('The product has been saved.'));
		$this->_redirect('cssubaccount/customer/list');
		return;
		
		
	}
	
	
	public function viewAction()
	{
		if(!$this->_getSession()->getVendorId())
			$this->_redirect('csmarketplace/account/login/');
	
		$this->loadLayout();
		$this->renderLayout();
	
	}
	
	public function requestAction()
	{
		if(!$this->_getSession()->getVendorId())
			$this->_redirect('csmarketplace/account/login/');
	
		$this->loadLayout();
		$this->renderLayout();
	
	}
	
	public function sendAction()
	{ 	
		//print_r($this->_getSession()->getVendor()->getData());die;
		if(!$this->_getSession()->getVendorId())
			$this->_redirect('csmarketplace/account/login/');
		
		$emails = $this->getRequest()->getPost('email');
		$messages = $this->getRequest()->getPost('msg');
		
		$customer = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getWebsite()->getId());
		
		
		$count = count($emails);
		for($i=0; $i<count($emails); $i++ )
		{
			$cid = $emails[$i];
			
			$savedRequests = Mage::getModel('cssubaccount/accountstatus')->getCollection()->addFieldToFilter('parent_vendor',$this->_getSession()->getVendorId())
								->addFieldToFilter('email',$cid)->getData();
			if((count($savedRequests) && $savedRequests[0]['status']==0) || (count($savedRequests) && $savedRequests[0]['status']==1))
				continue;
			
			$vendor_coll = Mage::getModel('csmarketplace/vendor')->loadByEmail($cid);
			if(count($vendor_coll))
				continue;

			$data = array();
			$data['parent_vendor'] =  $this->_getSession()->getVendorId();
			$data['email'] = $cid;
			$data['status'] = 0;
			$model = Mage::getModel('cssubaccount/accountstatus')->setData($data);
			$model->save();
			
			$emailTemplate  = Mage::getModel('core/email_template')
			->loadDefault('ced_cssubaccount_request_template');
			
			$collection = Mage::getModel('cssubaccount/accountstatus')->getCollection()->addFieldToFilter('parent_vendor',$this->_getSession()->getVendorId())
								->addFieldToFilter('email',$cid)->getData();
			$name = $customer->loadByEmail($emails[$i])->getName();
			
			$emailTemplateVariables = array();
			$emailTemplateVariables['vname'] = $this->_getSession()->getVendor()->getPublicName();
			$emailTemplateVariables['vid'] = $this->_getSession()->getVendorId();
			$emailTemplateVariables['cid'] = $collection[0]['id'];
			//$emailTemplateVariables['cname'] = $name;
			$emailTemplateVariables['company_address'] = $this->_getSession()->getVendor()->getCompanyAddress();
			$emailTemplateVariables['message'] = $messages[$i];
			//$emailTemplateVariables['created_at'] = $created_at;
			$emailTemplate->setSenderName($this->_getSession()->getVendor()->getPublicName());
			$emailTemplate->setSenderEmail($this->_getSession()->getVendor()->getEmail());
			$emailTemplate->setType('html');
			$emailTemplate->setTemplateSubject('New Request For Sub-Vendor');
			//print_r($emailTemplateVariables);die;
			try
			{
				$emailTemplate->send($emails[$i], $name, $emailTemplateVariables);
			}
			catch(Exception $e)
			{
				$errorMessage = $e->getMessage();
				echo $errorMessage;die('in catch');
			}
		}
		//$this->getLayout()->getBlock('head')->setTitle($this->__('Manage Customers'));
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('cssubaccount')->__('Request has been successfully sent to '.$count.' customers.'));
		$this->_redirect('cssubaccount/customer/list/');
		return;
	
	}
	
	
	public function createAction()
	{
		if(!$this->_getSession()->getSubVendorData()){
			Mage::getSingleton('customer/session')->logout();
			
		}
		
		if ($this->_getSession()->isLoggedIn() && $this->_getSession()->getSubVendorData()){
			if($this->_getSession()->getSubVendorData('email') != $this->_getSession()->getCustomer('email'))
				Mage::getSingleton('customer/session')->logout();
			else{
				/* if(Mage::getSingleton('core/session')->getParentVendor())
					Mage::getSingleton('core/session')->unsParentVendor(); */
				$this->_redirect('csmarketplace/vendor/index');
				return;
			}
		}
	
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	public function createPostAction()
	{ 
		$parentVendor = Mage::getSingleton('core/session')->getParentVendor();
		Mage::getSingleton('core/session')->unsParentVendor();
		try{
			if(!$parentVendor){ 
				Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__('Kindly click on the accept link from mail sent by seller.'));
				$this->_redirect('cssubaccount/customer/create/');
				return;
			 
			}
			$errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
			if (!$this->_validateFormKey()) {
				$this->_redirectError($errUrl);
				$this->_redirect('cssubaccount/customer/create/');
				return;
			}
	
			if (!$this->getRequest()->isPost()) {
				$this->_redirectError($errUrl);
				$this->_redirect('cssubaccount/customer/create/');
				return;
			}
			$password = $this->getRequest()->getPost('password');
			$email = $this->getRequest()->getPost('email');
			
			//$customer = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
			//$authentic = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->authenticate($email, $password);
			
			$vendor_coll = Mage::getModel('csmarketplace/vendor')->loadByEmail($email);
			
			$subvendor_coll = Mage::getModel('cssubaccount/cssubaccount')->load($email,'email')->getData();
			$request_coll = Mage::getModel('cssubaccount/accountstatus')->getCollection()->addFieldToFilter('email',$email);
			
			if(!count($request_coll)){ 
				Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__('Seller has not sent any request to '.$email.' mail Id.'));
				$this->_redirect('csmarketplace/account/login/');
				return;
			}
			
			if(!empty($vendor_coll) || !empty($subvendor_coll)){ 
				Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__($email.' Mail id allready exist.'));
				$this->_redirect('csmarketplace/account/login/');
				return;
			}
			
			$data = array();
			$data['parent_vendor'] = $parentVendor;//Mage::registry('vendorId');
			$data['first_name'] = $this->getRequest()->getPost('firstname');
			$data['last_name'] = $this->getRequest()->getPost('lastname');
			$data['middle_name'] = $this->getRequest()->getPost('middlename');
			$data['email'] = $this->getRequest()->getPost('email');
			$data['password'] = Mage::helper('core')->encrypt($this->getRequest()->getPost('password'));//md5($this->getRequest()->getPost('password'));
			$data['status'] = 0;
			$model = Mage::getModel('cssubaccount/cssubaccount')->setData($data);
			$model->save();
			$session = Mage::getSingleton("customer/session");
			$session->setParentVendor($parentVendor);
			$session->setSubvendorEmail($this->getRequest()->getPost('email'));
			
		}catch( Exception $e){
			$msg = $e->getMessage();
			Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__($msg));
			$this->_redirect('cssubaccount/customer/create/');
			return;
		}
		
		$this->_redirect('cssubaccount/vendor/approval');
		return;
	
	}
	
	protected function _getUrl($url, $params = array())
	{
		return Mage::getUrl($url, $params);
	}
	
	public function approveAction()
	{ 
		$id = $this->getRequest()->getParam('id');
		$subvendorIds = explode(',', $id);
		try{
			foreach($subvendorIds as $subvendorId){
				
				$model = Mage::getModel('cssubaccount/cssubaccount')->load($subvendorId)->setStatus(1);
				$model->save();
			}
		
		}catch(Exception $e){
			$msg = $e->getMessage();
			Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__($msg));
			$this->_redirect('cssubaccount/customer/list/');
			return;
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('cssubaccount')->__('Sub-Vendor Statuses are changed successfully'));
		$this->_redirect('cssubaccount/customer/list');
		return;
			
	}

	public function disapproveAction()
	{
		$id = $this->getRequest()->getParam('id');
		$subvendorIds = explode(',', $id);
		try{
			foreach($subvendorIds as $subvendorId){
				
				$model = Mage::getModel('cssubaccount/cssubaccount')->load($subvendorId)->setStatus(2);
				$model->save();
			}
		}catch(Exception $e){
			$msg = $e->getMessage();
			Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__($msg));
			$this->_redirect('cssubaccount/customer/list/');
			return;
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('cssubaccount')->__('Sub-Vendor Statuses are changed successfully'));
		$this->_redirect('cssubaccount/customer/list');
		return;
			
	}
	
	
}
