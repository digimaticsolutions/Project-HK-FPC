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

class Ced_CsCmsPage_VblockController extends Ced_CsMarketplace_Controller_AbstractController {	

	/**
	 * 
	 * Innitialize Vendor Cms Pages Listing
	 */
	public function indexAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
    }
    /**
     * 
     * Create New Vendor Cms Pages
     */
	public function newAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
    }
    /**
     * 
     * Saving Vendor Cms Pages ...
     */
    public function saveblockAction(){
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
    	if(!$this->_getSession()->getVendorId()) return;
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost('vblock');
    		$store = $this->getRequest()->getPost('vcmspage');
    		$VendorId = $this->_getSession()->getVendorId();
    		try{
    		$AdminApproval = Mage::getStoreConfig('ced_csmarketplace/vcmspage/vblockconfirmation');
	    	$pageapproval = 1;
	    	if($AdminApproval){
	    		$pageapproval = 0;
	    	}
    			if(sizeof($data)>0){
    				
    				$identifier =  $this->_getSession()->getVendor()->getShopUrl().'_'.$data['identifier'];
	    			$date = date("Y-m-d H:i:s"); 
	    			$Vendorblock = Mage::getModel('cscmspage/block');
	    			$Vendorblock->setTitle($data['title']);
	    			$Vendorblock->setIdentifier($identifier);
	    			$Vendorblock->setIsActive($data['status']);
	    			$Vendorblock->setContent($data['content']);
	    			$Vendorblock->setIsApprove($pageapproval);
	    			$Vendorblock->setVendorId($VendorId);
	    			$Vendorblock->setCreationTime($date);
	    			$Vendorblock->setUpdateTime($date);
	    			$Vendorblock->save();
	    			
	    			if($Vendorblock->getBlockId()!=null && $Vendorblock->getBlockId()>0){
	    				if(isset($store['store']) && sizeof($store['store'])>0){
	    					foreach($store['store'] as $storeId){
			    				$Vblockstore = Mage::getModel('cscmspage/vendorblock');
			    				$Vblockstore->setBlockId($Vendorblock->getBlockId());
			    				$Vblockstore->setStoreId($storeId);
			    				$Vblockstore->setvendorId($this->_getSession()->getVendorId());
			    				$Vblockstore->save();
	    					}
	    					$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('Block Page Created Successfully'));
			    			$this->_redirect('*/*/index');
		    				
	    				}		
		    		}
    			}	
    		}catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/new');
				return;
			}	
    	}
    }
    
	/**
	 * Vendor CMS grid for AJAX request
	 */
	public function gridAction()
	{
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Edit Vendor Block Page
	 */
	public function editAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId())
			return;
			
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Update Vendor Static Block Page
	 */
	public function updateblockAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId())
			return;
		if($this->getRequest()->getPost() && $this->getRequest()->getParam('block_id') && $this->getRequest()->getParam('block_id')>0){
			$block_id = $this->getRequest()->getParam('block_id');
			$data = $this->getRequest()->getPost('vblock');
    		$store = $this->getRequest()->getPost('vcmspage');
    		$VendorId = $this->_getSession()->getVendorId();
			$Vendorblock = Mage::getModel('cscmspage/block')->load($block_id);
			try{
				if(sizeof($Vendorblock)>0){
					$shopurl = explode('/',Mage::helper('cscmspage')->getVendorShopUrl());	
					$identifier = $shopurl['1'].'_'.$data['identifier'];
					$date = date("Y-m-d H:i:s"); 
	    			$Vendorblock->setTitle($data['title']);
	    			$Vendorblock->setIdentifier($identifier);
	    			$Vendorblock->setIsActive($data['status']);
	    			$Vendorblock->setContent($data['content']);
	    			$Vendorblock->setUpdateTime($date);
	    			$Vendorblock->save();
	    			$VendorBlockStore = Mage::getModel('cscmspage/vendorblock')->getCollection()
	    											->addFieldToFilter('vendor_id',$VendorId)
													->addFieldToFilter('block_id',$block_id);
					if(sizeof($VendorBlockStore)>0){
						foreach($VendorBlockStore as $block){
							$VendorBlock = Mage::getModel('cscmspage/vendorblock')->load($block->getId());
							$VendorBlock->delete();
						}
					}
					if(isset($store['store']) && sizeof($store['store'])>0){
		    				foreach($store['store'] as $storeId){
			    				$Vblockstore = Mage::getModel('cscmspage/vendorblock');
			    				$Vblockstore->setBlockId($Vendorblock->getBlockId());
			    				$Vblockstore->setStoreId($storeId);
			    				$Vblockstore->setvendorId($this->_getSession()->getVendorId());
			    				$Vblockstore->save();
		    				}
	    				$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('Block Page Update Successfully'));
		    			$this->_redirect('*/*/index');
						return;
					}	
	    			
				}else{
					$this->_getSession()->addError('Fail to update the Block Page!');
					$this->_redirect('*/*/index');
				}
			}catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/index');
				return;
			}
			
		}else{
			$this->_getSession()->addError('Fail to update the Cms Page!');
			$this->_redirect('*/*/index');
		}
		
	}
	
	/**
	 *  Mass Delete Cms Pages  
	 */
	public function massDeleteAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId()) return;
		
		$BlockIds = $this->getRequest()->getParam('block_id');
		$BlockIds = explode(',',$this->getRequest()->getParam('block_id'));
		
        if (!is_array($BlockIds)) {
            $this->_getSession()->addError($this->__('Please select Block Page(s).'));
        } else {
            if (!empty($BlockIds)) {
                try {
                	foreach ($BlockIds as $blockId) {
                        $block = Mage::getModel('cscmspage/block')->load($blockId);
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
	
}