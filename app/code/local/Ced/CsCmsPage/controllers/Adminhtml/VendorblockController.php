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
  * @package     Ced_CsCmsPage
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
 
class Ced_CsCmsPage_Adminhtml_VendorblockController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	/**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_BlockController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_title($this->__('Cscmspage'))->_title($this->__('Static Blocks'));
        $this->loadLayout()
            ->_setActiveMenu('csmarketplace/vendorblock')
            ->_addBreadcrumb(Mage::helper('cscmspage')->__('Vendor Static Blocks'), Mage::helper('cscmspage')->__('Vendor Static Blocks'));
        return $this;
    }
    
	/**
	 * Innitialize Block Grid
	 */
	public function indexAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		
		$this->loadLayout()
            ->_setActiveMenu('csmarketplace/vendorblock')
            ->_addBreadcrumb(Mage::helper('cscmspage')->__('Vendor Static Blocks'), Mage::helper('cscmspage')->__('Vendor Static Blocks'));
		$this->renderLayout();		
	}
    
    /**
     * Admin Save Cms Blocks 
     */
    public function saveAction()
    {
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
    	if($this->getRequest()->getPost() && $this->getRequest()->getParam('block_id') && $this->getRequest()->getParam('block_id')>0){
			$BlockId = $this->getRequest()->getParam('block_id');
    		$title = $this->getRequest()->getPost('title');
	    	$identifier = $this->getRequest()->getPost('identifier');
	    	$store = $this->getRequest()->getPost('stores');
	    	$is_active = $this->getRequest()->getPost('is_active');
	    	$content = $this->getRequest()->getPost('content');
	    	$date = date("Y-m-d H:i:s"); 
	      	$Block = Mage::getModel('cscmspage/block')->load($BlockId);
	      	try{
				if(sizeof($Block)>0){
	      			$VendorId = $Block->getVendorId();
	      			
	       			if($VendorId>0){
	       				$vendorCollection = Mage::getModel('csmarketplace/vendor')->load($VendorId);
						$identifier = $vendorCollection->getShopUrl().'_'.$identifier;
			       		$Block->setTitle($title);
			       		$Block->setIdentifier($identifier);
			       		$Block->setIsActive($is_active);
			       		$Block->setContent($content);
			       		$Block->setUpdateTime($date);
			       		$Block->save();
		       		
			       		if($Block->getBlockId()!=null && $Block->getBlockId()>0){
				       		
			       			$VendorBlockStore = Mage::getModel('cscmspage/vendorblock')->getCollection()
					    											->addFieldToFilter('vendor_id',$VendorId)
																	->addFieldToFilter('block_id',$BlockId);
							if(sizeof($VendorBlockStore)>0){
								foreach($VendorBlockStore as $block){
									$VendorBlock = Mage::getModel('cscmspage/vendorblock')->load($block->getId());
									$VendorBlock->delete();
								}
							}
				       		if(isset($store) && sizeof($store)>0){
				    			foreach($store as $storeId){
				    				$Vblockstore = Mage::getModel('cscmspage/vendorblock');
				    				$Vblockstore->setBlockId($Block->getBlockId());
				    				$Vblockstore->setStoreId($storeId);
				    				$Vblockstore->setVendorId($VendorId);
				    				$Vblockstore->save();
				    			}
				    			$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('Block Page Updated Successfully!'));
				       			Mage::getSingleton('adminhtml/session')->setFormData(false);
								// check if 'Save and Continue'
				                if ($this->getRequest()->getParam('back')) {
				                    $this->_redirect('*/*/edit', array('block_id' =>$BlockId, '_current'=>true));
				                    return;
				                }
				                $this->_redirect('*/*/index');
				    			return;
				                
				    		}		
				    	}
		       		}
	       		}
    		}catch(Exception $e){
    			$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/index');
				return;
    		}
    	}	 	
       	$this->_redirect('*/*/index');
    }
    
	/**
	 *  Approved Vendor Block Page By Admin
	 */
	public function approvedAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if($this->getRequest()->getParam('block_id') && $this->getRequest()->getParam('block_id')>0){
			$block_id = $this->getRequest()->getParam('block_id');
			try{
				$BlockPage = Mage::getModel('cscmspage/block')->load($block_id);
				$BlockPage->setIsApprove('1');
				$BlockPage->save();
				$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('Block Page Approved Successfully'));
			    $this->_redirect('*/*/index');
			    return;
			}catch(Exception $e)
			{
				$this->_getSession()->addError($e->getMessage());
			    $this->_redirect('*/*/index');
			    return;
			}
			
		}
		$this->_getSession()->addError(Mage::helper('cscmspage')->__('Failed TO Approve Block Page'));
		$this->_redirect('*/*/index');
	}
	
	/**
	 *  Admin Dis approved Vendor Block Page
	 */
	
	public function disapprovedAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if($this->getRequest()->getParam('block_id') && $this->getRequest()->getParam('block_id')>0){
			$block_id = $this->getRequest()->getParam('block_id');
			try{
				$BlockPage = Mage::getModel('cscmspage/block')->load($block_id);
				$BlockPage->setIsApprove('0');
				$BlockPage->save();
				$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('Block Page Disapproved Successfully'));
			    $this->_redirect('*/*/index');
			    return;
			}catch(Exception $e)
			{
				$this->_getSession()->addError($e->getMessage());
			    $this->_redirect('*/*/index');
			    return;
			}
			
		}
		$this->_getSession()->addError(Mage::helper('cscmspage')->__('Fail TO Disapprove The Cms Page'));
		$this->_redirect('*/*/index');
	}
	
	/**
	 *  Mass Delete Cms  Block Pages  
	 */
	public function massDeleteAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$BlockIds = $this->getRequest()->getParam('block_id');
		
		if (!is_array($BlockIds)) {
            $this->_getSession()->addError($this->__('Please select Block Page(s).'));
        } else {
            if (!empty($BlockIds)) {
                try {
                	foreach ($BlockIds as $blockId) {
                        $block = Mage::getSingleton('cscmspage/block')->load($blockId);
                        $block->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d Block Pages(s) have been deleted.', count($BlockIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
	}
	
	/**
	 * Mass Approval Block Pages 
	 */
	
	public function massApproveAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$BlockIds = $this->getRequest()->getParam('block_id');
        if (!is_array($BlockIds)) {
            $this->_getSession()->addError($this->__('Please select Block Page(s).'));
        } else {
            if (!empty($BlockIds)) {
                try {
                	foreach ($BlockIds as $blockId) {
                        $block = Mage::getModel('cscmspage/block')->load($blockId);
                       	$block->setIsApprove('1');
						$block->save();                       
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d Block(s) have been Approved.', count($BlockIds))
                    );
                } catch (Exception $e) {
                	$this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
	}
	
	/**
	 * Mass Disapproval Cms Block Pages 
	 */
	
	public function massdisApproveAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$BlockIds = $this->getRequest()->getParam('block_id');
        
        if (!is_array($BlockIds)) {
            $this->_getSession()->addError($this->__('Please select Block Page(s).'));
        } else {
            if (!empty($BlockIds)) {
                try {
                	foreach ($BlockIds as $blockId) {
                        $block = Mage::getModel('cscmspage/block')->load($blockId);
                        $block->setIsApprove('0');
						$block->save();
                        
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d Block Pages(s) have been Disapproved.', count($blockId))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
	}
	
	
	/**
     * Edit CMS block
     */
    public function editAction()
    {
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$this->_title($this->__('Cscmspage'))->_title($this->__('Static Blocks'));
    	$this->loadLayout()
            ->_setActiveMenu('csmarketplace/vendorblock')
            ->_addBreadcrumb(Mage::helper('cscmspage')->__('Edit Vendor Static Blocks'), Mage::helper('cscmspage')->__('Edit Vendor Static Blocks'));   	
    	$this->renderLayout();   
    }
	/**
     * Delete Cms Block action
     */
    public function deleteAction()
    {
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
       	$BlockId = $this->getRequest()->getParam('block_id');
       	if($BlockId>0){
       		$Block = Mage::getModel('cscmspage/block')->load($BlockId);
       		$Block->delete();
       		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cscmspage')->__('Static Block Deleted Successfully!'));
       	}
       	$this->_redirect('*/*/index');
    }
}