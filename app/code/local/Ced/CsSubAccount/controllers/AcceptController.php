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



class Ced_CsSubAccount_AcceptController extends Mage_Core_Controller_Front_Action
{
	public function acceptAction()
	{ 
		$email = Mage::getModel('cssubaccount/accountstatus')->load($this->getRequest()->getParam('cid'));
		$savedRequests = Mage::getModel('cssubaccount/accountstatus')->getCollection()->addFieldToFilter('email',$email->getEmail())
							->addFieldToFilter('status',1);
		if(count($savedRequests))
		{
			Mage::getSingleton('core/session')->addError(Mage::helper('cssubaccount')->__('You have already associated with another seller.'));
			$this->_redirect("/");
			
		}
		$model = Mage::getModel('cssubaccount/accountstatus')->load($this->getRequest()->getParam('cid'))->setStatus(1);
		try {
			$model->save();				
			Mage::getSingleton('core/session')->setParentVendor($this->getRequest()->getParam('vid'));
			$msg = 'You have accepted the seller request. Now Create your seller account';
			
		} catch(Exception $e){
			$msg= $e->getMessage();
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('cssubaccount')->__($msg));
		$this->_redirect("cssubaccount/customer/create");
		
		
	
	}
	
	
	public function rejectAction()
	{
		$model = Mage::getModel('cssubaccount/accountstatus')->load($this->getRequest()->getParam('cid'))->setStatus(2);
		try {
			$model->save();
	
			
		} catch (Exception $e){
			echo $e->getMessage();
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('cssubaccount')->__('You have successfully rejected the seller request'));
		$this->_redirect("/");
	
	}
}
