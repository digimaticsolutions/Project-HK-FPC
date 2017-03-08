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
 
class Ced_CsCmsPage_Adminhtml_VendorcmsController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	/**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_BlockController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_title($this->__('Cscmspage'))->_title($this->__('Vendor Cms'));
        $this->loadLayout()
            ->_setActiveMenu('csmarketplace/vendorcms')
            ->_addBreadcrumb(Mage::helper('cscmspage')->__('Vendor Cms'), Mage::helper('cscmspage')->__('Vendor Cms'));
        return $this;
    }
	/**
	 * 
	 * Innitialize Cms Pages Grid
	 */
	public function IndexAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$this->loadLayout()
            ->_setActiveMenu('csmarketplace/vendorcms')
            ->_addBreadcrumb(Mage::helper('cscmspage')->__('Vendor Cms'), Mage::helper('cscmspage')->__('Vendor Cms'));
        $this->renderLayout();
	}
	
/**
     * Save action
     */
    public function saveAction()
    {
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
    	// check if data sent
        
    	if ($data = $this->getRequest()->getPost()) {
			$checkHome = array();
    		//init model and set data
            $Vendorcmspage = Mage::getModel('cscmspage/cmspage');

            if ($id = $this->getRequest()->getParam('page_id')) {
                $Vendorcmspage->load($id);
            }
			$VendorId = $Vendorcmspage->getVendorId();
			$vendorCollection = Mage::getModel('csmarketplace/vendor')->load($VendorId);
			
			$identifier = 'vendorshop/'.$vendorCollection->getShopUrl().'/'.$data['identifier'];
			
			$date = date("Y-m-d H:i:s"); 
			$Vendorcmspage->setTitle($data['title']);
    		$Vendorcmspage->setIdentifier($identifier);
    		$Vendorcmspage->setIsActive($data['is_active']);
    		$Vendorcmspage->setContentHeading($data['content_heading']);
    		$Vendorcmspage->setContent($data['content']);
    		$Vendorcmspage->setRootTemplate($data['root_template']);
    		$Vendorcmspage->setLayoutUpdateXml($data['layout_update_xml']);
    		$Vendorcmspage->setMetaKeywords($data['meta_keywords']);
    		$Vendorcmspage->setMetaDescription($data['meta_description']);
    		$Vendorcmspage->setUpdateTime($date);
            try {
                if($Vendorcmspage->save()){
	                $VendorCmsStore = Mage::getModel('cscmspage/vendorcms')->getCollection()
		    											->addFieldToFilter('vendor_id',$VendorId)
														->addFieldToFilter('page_id',$id);
					
														
		            if(sizeof($VendorCmsStore)>0){
						foreach($VendorCmsStore as $cmsstore){
							$CmsStore = Mage::getModel('cscmspage/vendorcms')->load($cmsstore->getId());
							$checkHome[$cmsstore->getStoreId()] = $cmsstore->getIsHome();
							$CmsStore->delete();
						}
					}
				
					if(isset($data['stores']) && sizeof($data['stores'])>0){
			    				foreach($data['stores'] as $storeId){
				    				$Vcmsstore = Mage::getModel('cscmspage/vendorcms');
				    				$Vcmsstore->setPageId($id);
				    				if($checkHome[$storeId] == '1'){
				    					$Vcmsstore->setIsHome('1');
				    				}else{
				    					$Vcmsstore->setIsHome('0');
				    				}
				    				$Vcmsstore->setStoreId($storeId);
				    				$Vcmsstore->setvendorId($VendorId);
				    				$Vcmsstore->save();
			    				}
		    				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cscmspage')->__('The page has been saved.'));
			    			Mage::getSingleton('adminhtml/session')->setFormData(false);
							// check if 'Save and Continue'
			                if ($this->getRequest()->getParam('back')) {
			                    $this->_redirect('*/*/edit', array('page_id' => $id, '_current'=>true));
			                    return;
			                }
					}	
					// go to grid
	                $this->_redirect('*/*/');
	                return;
            	}
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
            		print_r($e);die;
                $this->_getSession()->addException($e,
                    Mage::helper('cms')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
	/**
     * Delete action
     */
    public function deleteAction()
    {
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('page_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('cscmspage/cmspage');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The page has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('page_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a page to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
	
	/**
	 *  Approved Vendor Cms Page By Admin
	 */
	public function approvedAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if($this->getRequest()->getParam('page_id') && $this->getRequest()->getParam('page_id')>0){
			$page_id = $this->getRequest()->getParam('page_id');
			try{
				$CmsPage = Mage::getModel('cscmspage/cmspage')->load($page_id);
				$CmsPage->setIsApprove('1');
				$CmsPage->save();
				$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('CMS Page Approved Successfully'));
			    $this->_redirect('*/*/index');
			    return;
			}catch(Exception $e)
			{
				$this->_getSession()->addError($e->getMessage());
			    $this->_redirect('*/*/index');
			    return;
			}
			
		}
		$this->_getSession()->addError(Mage::helper('cscmspage')->__('Failed TO Approve The Cms Page'));
		$this->_redirect('*/*/index');
	}
	/**
	 *  Admin Dis approved Vendor Cms Page
	 */
	public function disapprovedAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		if($this->getRequest()->getParam('page_id') && $this->getRequest()->getParam('page_id')>0){
			$page_id = $this->getRequest()->getParam('page_id');
			try{
				$CmsPage = Mage::getModel('cscmspage/cmspage')->load($page_id);
				$CmsPage->setIsApprove('0');
				$CmsPage->save();
				$this->_getSession()->addSuccess(Mage::helper('cscmspage')->__('CMS Page Disapproved Successfully'));
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
	 *  Mass Delete Cms Pages  
	 */
	public function massDeleteAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$CmsIds = $this->getRequest()->getParam('page_id');
        if (!is_array($CmsIds)) {
            $this->_getSession()->addError($this->__('Please select Cms Page(s).'));
        } else {
            if (!empty($CmsIds)) {
                try {
                	$count = 0;
                    foreach ($CmsIds as $cmsId) {
                        $cms = Mage::getModel('cscmspage/cmspage')->load($cmsId);
                        $cms->delete();
                        $count++;
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
	
	/**
	 * Mass Approval Cms Pages 
	 */
	public function massApproveAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$CmsIds = $this->getRequest()->getParam('page_id');
        if (!is_array($CmsIds)) {
            $this->_getSession()->addError($this->__('Please select Cms Page(s).'));
        } else {
            if (!empty($CmsIds)) {
                try {
                	$count = 0;
                    foreach ($CmsIds as $cmsId) {
                        $cms = Mage::getModel('cscmspage/cmspage')->load($cmsId);
                       	$cms->setIsApprove('1');
						$cms->save();
                       
                        $count++;
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of '.$count.' CMS Pages(s) have been Approved.')
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
	}
	/**
	 * Mass Dis Approval Cms Pages 
	 */
	public function massdisApproveAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$CmsIds = $this->getRequest()->getParam('page_id');
        
        if (!is_array($CmsIds)) {
            $this->_getSession()->addError($this->__('Please select Cms Page(s).'));
        } else {
            if (!empty($CmsIds)) {
                try {
                	$count = 0;
                    foreach ($CmsIds as $cmsId) {
                        $cms = Mage::getModel('cscmspage/cmspage')->load($cmsId);
                        $cms->setIsApprove('0');
						$cms->save();
                        $count++;
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of '.$count.' CMS Pages(s) have been Disapproved.')
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
	}
	/**
	 * Edit Vendor Cms Page
	 */
	public function editAction(){
		if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_redirect('cms/index/noRoute');
			return;
		}
		$this->loadLayout()
            ->_setActiveMenu('csmarketplace/vendorcms')
            ->_addBreadcrumb(Mage::helper('cscmspage')->__('Edit Vendor Cms'), Mage::helper('cscmspage')->__('Edit Vendor Cms'));
        
		$this->renderLayout();
	}
}