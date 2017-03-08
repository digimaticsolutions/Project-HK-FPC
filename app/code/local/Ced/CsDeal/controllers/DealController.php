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
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Deal controller
 *
 * @category   Ced
 * @package    Ced_CsDeal
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 *
 */
class Ced_CsDeal_DealController extends  Ced_CsMarketplace_Controller_AbstractController {

	public function createAction(){
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId) {
			return;
		}
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->_title($this->__("Deal"));
		$this->_title($this->__("Create Deal"));
		$this->renderLayout();
		
	}
	public function listAction(){
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId) {
			return;
		}
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->_title($this->__("Deal"));
		$this->_title($this->__("Vendor Deals"));
		$this->renderLayout();
	}
	public function massDeleteAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		if(!$vendorId)
			return;
		$dealIds = explode(',',$this->getRequest()->getParam('deal_id'));
		if (!is_array($dealIds)) {
			$this->_getSession()->addError($this->__('Please select product(s).'));
		} else {
			if (!empty($dealIds)) {
				try {
					foreach ($dealIds as $dealid) {
						$model=Mage::getModel('csdeal/deal')->load($dealid);
						Mage::dispatchEvent('ced_csdeal_delete_predispatch_action', array('deal' => $model,
				));
						$model->delete();
						$model->save();
					}
					Mage::getSingleton('customer/session')->addSuccess('Deals Deleted Successfully');
				} catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
			}
		}
		$this->_redirect('*/*/list');
	}
	
	public function DeleteAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		if(!$vendorId)
			return;
		$dealIds = $this->getRequest()->getParam('deal_id');

				try {
					if($dealIds) {
						$model=Mage::getModel('csdeal/deal')->load($dealIds);
						Mage::dispatchEvent('ced_csdeal_delete_predispatch_action', array('deal' => $model,
				));
						$model->delete();
						$model->save();
						Mage::getSingleton('customer/session')->addSuccess('Deals Deleted Successfully');
					}
					
				} catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
		$this->_redirect('*/*/list');
	}
	/**
	 * Update product(s) status action
	 *
	 */
	public function massEnableAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		if(!$vendorId)
			return;
		$dealIds = explode(',',$this->getRequest()->getParam('deal_id'));
		if (!is_array($dealIds)) {
			Mage::getSingleton('customer/session')->addError($this->__('Please select product(s).'));
		} else {
			if (!empty($dealIds)) {
				try {
					foreach ($dealIds as $dealid) {
						$model=Mage::getModel('csdeal/deal')->load($dealid);
						$model->setStatus(Ced_CsDeal_Model_Status::STATUS_ENABLED);
						$model->save();
					}
					Mage::getSingleton('customer/session')->addSuccess('Deals Status Changed Successfully');
				} catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
			}
		}
		$this->_redirect('*/*/list');
	}
	public function massDisableAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		if(!$vendorId)
			return;
		$dealIds = explode(',',$this->getRequest()->getParam('deal_id'));
		if (!is_array($dealIds)) {
			$this->_getSession()->addError($this->__('Please select product(s).'));
		} else {
			if (!empty($dealIds)) {
				try {
					foreach ($dealIds as $dealid) {
						$model=Mage::getModel('csdeal/deal')->load($dealid);
						$model->setStatus(Ced_CsDeal_Model_Status::STATUS_DISABLED);
						$model->save();
					}
					Mage::getSingleton('customer/session')->addSuccess('Deals Status Changed Successfully');
				} catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
			}
		}
		$this->_redirect('*/*/list');
	}
	
	public function gridAction() {
		$this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('csdeal/dealgrid')->toHtml()
        ); 
	}
	public function creategridAction() {
		$this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('csdeal/grid')->toHtml()
        ); 
	}

	public function expireAction(){
	$post_data=$this->getRequest()->getPost('product_id');	
	 $product=Mage::getModel('catalog/product')->load($post_data);
        try{
        $product->setSpecialPrice('');
        $product->setSpecialFromDate('');
        $product->setSpecialFromDateIsFormated(true);
        $product->setSpecialToDate('');
        $product->setSpecialToDateIsFormated(true);
        $product->save();
        $deal=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('product_id',$post_data)->getFirstItem();
        $deal->delete();
        }catch(Exception $e){
            Mage::getSingleton('customer/session')->addError(Mage::helper('core')->__('%s',$e->getMessage()));
        } 
	}


	
	public function dealpopupAction(){
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId) {
			return;
		}
		$this->loadLayout();
		$this->_title($this->__("Deal"));
		$this->_title($this->__("Create Deal "));
		$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
				if ($navigationBlock) {
					$navigationBlock->setActive('csdeal/deal/create');
				}
		$this->renderLayout();
	}

	/**
	 * Product edit form
	 */
	public function editAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId) {
			return;
		}
				$id = $this->getRequest()->getParam("deal_id");
				$model = Mage::getModel("csdeal/deal")->load($id);
				if ($model->getId()) {
					Mage::register("csdeal_data", $model);
					$this->loadLayout();
					 $this->_title($this->__("Deal"));
				    $this->_title($this->__("Edit Deal"));
				    $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
					if ($navigationBlock) {
						$navigationBlock->setActive('csdeal/deal/list');
					}
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton('customer/session')->addError(Mage::helper("csdeal")->__("Deal does not exist."));
					$this->_redirect("*/*/list");
					return;
				}
	}
	

	public function saveAction(){
		if($this->getRequest()->isPost()){
			$post_data=$this->getRequest()->getPost();
			$start_date_format=$post_data['start_date'];
			$timestamp = strtotime($start_date_format);
			$post_data['start_date']= date("Y-m-d H:i:s", $timestamp);
			$end_date_format=$post_data['end_date'];
			$timestamp = strtotime($end_date_format);
			$post_data['end_date']= date("Y-m-d H:i:s", $timestamp);
			if($post_data['days']==0){
				$spdata=implode(',',$post_data['specificdays']);
				$post_data['specificdays']=$spdata;
			}
			$needApproval=Mage::helper('csdeal')->isApprovalNeeded();
			if($needApproval && !$this->getRequest()->getParam('deal_id'))
			$post_data['admin_status']=Ced_CsDeal_Model_Deal::STATUS_PENDING;	
			else
			$post_data['admin_status']=Ced_CsDeal_Model_Deal::STATUS_APPROVED;		
			$model=Mage::getModel('csdeal/deal');
			$model->addData($post_data)->setDealId($this->getRequest()->getParam('deal_id'));
			Mage::dispatchEvent('ced_csdeal_create_predispatch_action', array('deal' => $model,
				));
			$model->save();
			Mage::getSingleton('customer/session')->addSuccess('Deal created Successfully');
			$this->_redirect('*/*/list');		
            return;
		}else{
			$this->_redirect('*/*/create');	
		}
	}
	
}
