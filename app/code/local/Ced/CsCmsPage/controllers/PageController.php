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

class Ced_CsCmsPage_PageController extends Mage_Core_Controller_Front_Action {	
	
	/**
     * Initialize requested vendor object
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    protected function _initVendor()
    {
        Mage::dispatchEvent('csmarketplace_controller_vshops_init_before', array('controller_action' => $this));
        
        if(!Mage::helper('csmarketplace/acl')->isEnabled())
        	return false;
        $shopUrl = Mage::getModel('csmarketplace/vendor')->getShopUrlKey($this->getRequest()->getParam('shop_url',''));
        if (!strlen($shopUrl)) {
            return false;
        }

        $vendor = Mage::getModel('csmarketplace/vendor')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByAttribute('shop_url',$shopUrl);

        if (!Mage::helper('csmarketplace')->canShow($vendor)) {
            return false;
        }
        Mage::register('current_vendor', $vendor);
        
        try {
            Mage::dispatchEvent(
                'csmarketplace_controller_vshops_init_after',
                array(
                    'vendor' => $vendor,
                    'controller_action' => $this
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $vendor;
    }
	
	
	/**
     * View CMS page action
     *
     */
    public function viewAction()
    {	
    	
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_forward('noRoute');
			return;
		}
		$vid = '';
		if($this->getRequest()->getParam('vid')>0){
			
			$vid = $this->getRequest()->getParam('vid');
            $vendor= Mage::getModel('csmarketplace/vendor')->load($vid);
			$vendor_object = new Varien_Object();
			$vendor_object->setId($vid);
			Mage::register('current_vendor',$vendor);
		}else if(Mage::getSingleton('customer/session')->getVendorId()){
			
				$vid = Mage::getSingleton('customer/session')->getVendorId();
				$vendor= Mage::getModel('csmarketplace/vendor')->load($vid);
			
				$vendor_object = new Varien_Object();
				$vendor_object->setId($vid);
				
				Mage::register('current_vendor',$vendor);
			}
			
		$pageId = $this->getRequest()
						->getParam('page_id', $this->getRequest()->getParam('id', false));
		
		if(Mage::registry('current_vendor') == "")
		{
			
			$page= Mage::getModel('cscmspage/cmspage')->load($pageId);
			$vendor_Id= $page['vendor_id'];
			 
			$vendor1= Mage::getModel('csmarketplace/vendor')->load($vendor_Id);
		
			Mage::register('current_vendor',$vendor1);
		}
		
        if (!Mage::helper('cscmspage/cmspage')->renderPage($this, $pageId)) {
        	
        	$this->_forward('noRoute');
        }
        
    }
    
    public function indexAction(){
    	
    	/*print_r($Cms = Mage::getModel('cscmspage/cmspage')->getCollection()->addFieldToFilter('vendor_id',11)->getData());die('--here');*/
    	if(!Mage::helper('cscmspage')->isEnabled()){
			$this->_forward('noRoute');
			return;
		}
    	$pageId = '';
    	if ($vendor = $this->_initVendor()) {
    		if(sizeof($vendor)>0 && $vendor->getEntityId()>0){
    			$Cms = Mage::getModel('cscmspage/cmspage')->getCollection()
    						->addFieldToFilter('vendor_id',$vendor->getEntityId())
    						->addFieldToFilter('is_active','1')
    						->addFieldToFilter('is_approve','1');
    				
    		
    			if(sizeof($Cms)>0){
    				foreach($Cms as $cmspage ){
    					$CmsStores = Mage::getModel('cscmspage/vendorcms')->getCollection()
    									->addFieldToFilter('page_id',$cmspage->getPageId())
    									->addFieldToFilter('is_home','1')
    									->addFieldToFilter('store_id',Mage::app()->getStore()->getId());
    				
    					if(count($CmsStores)>0){
    						$pageId = $cmspage->getPageId();
    					}else{
    						$CmsStores = Mage::getModel('cscmspage/vendorcms')->getCollection()
    									->addFieldToFilter('page_id',$cmspage->getPageId())
    									->addFieldToFilter('is_home','1')
    									->addFieldToFilter('store_id','0');
    						if(count($CmsStores)>0){
    							$pageId = $cmspage->getPageId();
    						}		
    					}
    				}
    			}	
    		
    			if($pageId>0){
    				/* if (!Mage::helper('cscmspage/cmspage')->renderPage($this, $pageId)) {
    					$this->_forward('noRoute');
    				} */
    		 	$cms = Mage::getModel('cscmspage/cmspage')->load($pageId);
    				$urlModel = Mage::getModel('core/url');
    				$href = $urlModel->getUrl(
    						$cms->getIdentifier(), array(
    								'_current' => false,
    								'_query' => '___store='.'en'.'&vid='.$cms->getVendorId()
    						)
    				);
    				//echo $href; die("dvfkl");
    			//	$this->_forward($href);
    				$this->_redirect($cms->getIdentifier()); 
			       
	    		}else{
	    			
	    			/* 
	    			$this->loadLayout();
		    		$update = $this->getLayout()->getUpdate();
					$update->addHandle('default');
					$this->addActionLayoutHandles();
					$update->addHandle('csmarketplace_vshops_view');
	    			$this->loadLayoutUpdates();
					$this->generateLayoutXml();
					$this->generateLayoutBlocks();
					$this->getLayout()->getBlock('head')->setTitle(Mage::helper('cscmspage')->__('Vendor Shop Page'));
					$this->renderLayout(); */	    
	    			$shopUrl = Mage::getModel('csmarketplace/vendor')->load($vendor->getId())->getShopUrl();
	    			$this->_redirect('csmarketplace/vshops/view/shop_url/'.$shopUrl);
				}
    		}
		}
        else{
        	$this->_forward('noRoute');
        }
    }
	
}