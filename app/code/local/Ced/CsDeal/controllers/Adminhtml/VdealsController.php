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
 * @category    Ced;
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsDeal_Adminhtml_VdealsController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	/**
	 * Vendor's All products grid page
	 */
	public function indexAction(){		
	 $this->loadLayout()->_setActiveMenu('csmarketplace/csdeal');
	 $this->_addContent($this->getLayout()->createBlock('csdeal/adminhtml_vdeals'));
	 $this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Deals'));
	 $this->renderLayout();	
	}
	


	
	/**
	 * Vendor's all products grid action
	 */
	public function gridAction() {
		$this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('csdeal/adminhtml_vdeals_grid')->toHtml()
        ); 
	}
	


	/**
	 * Vendor's product mass status change action
	 */
	public function massStatusAction()
	{
		$checkstatus=$this->getRequest()->getParam('status');
		$productIds=$this->getRequest()->getParam('deal_id');
		if (!is_array($productIds)) {
			$this->_getSession()->addError(Mage::helper('csdeal')->__('Please select deal(s).'));
		}
		else if(!empty($productIds)&& $checkstatus!='') {
			try{
				$errors=Mage::getModel('csdeal/deal')->changeVdealStatus($productIds,$checkstatus);
				if($errors['success'])
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csdeal')->__("Status changed Successfully"));
				if($errors['error'])
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csdeal')->__('Can\'t process approval/disapproval for some products.Some of Product\'s vendor(s) are disapproved or not exist.'));
			}
			catch(Exception $e)	{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
			}
		}			
		$this->getResponse()->setRedirect($this->_getRefererUrl());
	}
	public function changeStatusAction()
	{
		$checkstatus=$this->getRequest()->getParam('status');
		$dealId=$this->getRequest()->getParam('deal_id');	
		if( $dealId > 0 && $checkstatus!='') {
			try{
				$errors=Mage::getModel('csdeal/deal')->changeVdealStatus($dealId,$checkstatus);
				if($errors['success'])
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__("Status changed Successfully"));
				if($errors['error'])
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__("Can't process approval/disapproval for the Product.The Product's vendor is disapproved or not exist."));
			}
			catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
			}
		}		
		$this->getResponse()->setRedirect($this->_getRefererUrl());
	}
	
}