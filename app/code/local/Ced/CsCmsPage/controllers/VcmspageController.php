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

class Ced_CsCmsPage_VcmspageController extends Ced_CsMarketplace_Controller_AbstractController {	

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
    public function savecmsAction(){
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
    	if(!$this->_getSession()->getVendorId()) return;
    	
    	$AdminApproval = Mage::getStoreConfig('ced_csmarketplace/vcmspage/confirmation');
    	$pageapproval = 1;
    	if($AdminApproval){
    		$pageapproval = 0;
    	}
    	if(!$this->_getSession()->getVendorId()) return;
    	
    	$VendorId = $this->_getSession()->getVendorId();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost('vcmspage');
    		
    		try{
    			if(sizeof($data)>0){
	    			$date = date("Y-m-d H:i:s"); 
	    			$identifier = Mage::helper('cscmspage')->getVendorShopUrl().$data['urlkey'];
	    			$Vendorcmspage = Mage::getModel('cscmspage/cmspage');
	    			$Vendorcmspage->setTitle($data['title']);
	    			$Vendorcmspage->setIdentifier($identifier);
	    			$Vendorcmspage->setIsActive($data['status']);
	    			$Vendorcmspage->setContentHeading($data['cheading']);
	    			$Vendorcmspage->setContent($data['content']);
	    			$Vendorcmspage->setRootTemplate($data['layout']);
	    			$Vendorcmspage->setLayoutUpdateXml($data['layout_xml']);
	    			$Vendorcmspage->setMetaKeywords($data['meta_keywords']);
	    			$Vendorcmspage->setMetaDescription($data['meta_description']);
	    			$Vendorcmspage->setData('custom_theme',$data['theme']);
	    			$Vendorcmspage->setIsApprove($pageapproval);
	    			$Vendorcmspage->setVendorId($VendorId);
	    			$Vendorcmspage->setCreationTime($date);
	    			$Vendorcmspage->setUpdateTime($date);
	    			$Vendorcmspage->save();
	    			
	    			if($Vendorcmspage->getPageId()!=null && $Vendorcmspage->getPageId()>0){
	    				if(isset($data['store']) && sizeof($data['store'])>0){
	    					foreach($data['store'] as $storeId){
			    				$Vcmsstore = Mage::getModel('cscmspage/vendorcms');
			    				$Vcmsstore->setPageId($Vendorcmspage->getPageId());
			    				$Vcmsstore->setStoreId($storeId);
			    				$Vcmsstore->setVendorId($this->_getSession()->getVendorId());
			    				$Vcmsstore->setIsHome($data['is_home']);
			    				$Vcmsstore->save();
	    					}
	    					$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('CMS Page Created Successfully'));
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
		$this->getResponse()->setBody(
             $this->getLayout()->createBlock('Ced_CsCmsPage_Block_Grid')->toHtml()
      );
		//$this->renderLayout();
	}
	
	/**
	 * Vendor Edit CMS Page
	 */
	public function editAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId())
			return;

		if($this->getRequest()->getParam('page_id')>0){
			$page_id = $this->getRequest()->getParam('page_id');
			$Vendorcmspage = Mage::getModel('cscmspage/cmspage')->load($page_id);
			if(sizeof($Vendorcmspage->getData())>0){
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');
				$this->renderLayout();
			}else{
				$this->_redirect('*/*/');
			}
		}else{
			$this->_redirect('*/*/');
		}
		
	}
	
	/**
	 * Update Vendor CMS Page
	 */
	public function updatecmsAction(){
		
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if(!$this->_getSession()->getVendorId())
			return;
			
		$VendorId = $this->_getSession()->getVendorId();	
		if($this->getRequest()->getPost() && $this->getRequest()->getParam('page_id') && $this->getRequest()->getParam('page_id')>0){
			$page_id = $this->getRequest()->getParam('page_id');
			$Vendorcmspage = Mage::getModel('cscmspage/cmspage')->load($page_id);
			try{
				if(sizeof($Vendorcmspage)>0){
					$data = $this->getRequest()->getPost('vcmspage');
					$date = date("Y-m-d H:i:s"); 
					$identifier = Mage::helper('cscmspage')->getVendorShopUrl().$data['urlkey'];
	    			$Vendorcmspage->setTitle($data['title']);
	    			$Vendorcmspage->setIdentifier($identifier);
	    			$Vendorcmspage->setIsActive($data['status']);
	    			$Vendorcmspage->setContentHeading($data['cheading']);
	    			$Vendorcmspage->setContent($data['content']);
	    			$Vendorcmspage->setRootTemplate($data['layout']);
	    			$Vendorcmspage->setLayoutUpdateXml($data['layout_xml']);
	    			$Vendorcmspage->setMetaKeywords($data['meta_keywords']);
	    			$Vendorcmspage->setMetaDescription($data['meta_description']);
	    			$Vendorcmspage->setData('custom_theme',$data['theme']);
	    			$Vendorcmspage->setUpdateTime($date);
	    			$Vendorcmspage->save();
	    			$VendorCmsStore = Mage::getModel('cscmspage/vendorcms')->getCollection()
	    											->addFieldToFilter('vendor_id',$VendorId)
													->addFieldToFilter('page_id',$page_id);
					
					if(sizeof($VendorCmsStore)>0){
						foreach($VendorCmsStore as $cmsstore){
							$CmsStore = Mage::getModel('cscmspage/vendorcms')->load($cmsstore->getId());
							$CmsStore->delete();
						}
					}
					if(isset($data['store']) && sizeof($data['store'])>0){
		    				foreach($data['store'] as $storeId){
			    				$Vcmsstore = Mage::getModel('cscmspage/vendorcms');
			    				$Vcmsstore->setPageId($page_id);
			    				$Vcmsstore->setStoreId($storeId);
			    				$Vcmsstore->setvendorId($VendorId);
			    				$Vcmsstore->setIsHome($data['is_home']);
			    				$Vcmsstore->save();
		    				}
	    				$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('CMS Page Update Successfully'));
		    			$this->_redirect('*/*/index');
		    			return;
					}	
	    			
				}else{
					$this->_getSession()->addError('Fail to update the Cms Page!');
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
	 * check vendor Cms Home Page Stores and Default Activation.
	 */
	/*public function checkHomeCmspage($VendorId,$Stores,$is_Home,$is_active){
	    $status = 0;
    	if($is_Home == '1' && $is_active == '1'){	
    		$CmsStore = Mage::getModel('cscmspage/vendorcms')->getCollection()
	    							->addFieldToFilter('vendor_id',$VendorId)
	    							->addFieldToFilter('is_home','1');
	    	if(sizeof($CmsStore)>0 && sizeof($Stores)>0){						
		    	foreach($CmsStore as $pagestore){
					if(in_array($pagestore->getStoreId(),$Stores))	{
						$Cms = Mage::getModel('cscmspage/cmspage')->load($pagestore->getPageId());
						if($Cms->getIsActive()){
							return $status = 1;
						}
					}	
				}
	    	}
    	}	
	    return $status;
    } */
	
	/**
	 *  Mass Delete Cms Pages  
	 */
	public function massDeleteAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId)
			return;
		
		$CmsIds = $this->getRequest()->getParam('page_id');	
	
		$CmsIds = explode(',',$this->getRequest()->getParam('page_id'));
		
		if (!is_array($CmsIds)) {
            $this->_getSession()->addError($this->__('Please select Cms Page(s).'));
        } else {
            if (!empty($CmsIds)) {
                try {
                	$count = 0;
                    foreach ($CmsIds as $cmsId) {
                      try{
						++$count;
                      	$cms = Mage::getSingleton('cscmspage/cmspage')->load($cmsId);
                        $cms->delete();
                     
                    	}catch (Exception $e){
                    		echo $e->getMessage();
                    	}
                        
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of '.$count.' CMS Pages(s) have been deleted.')
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
	}
}