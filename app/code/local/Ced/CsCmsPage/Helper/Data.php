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
 

class Ced_CsCmsPage_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_NODE_PAGE_TEMPLATE_FILTER     = 'global/cms/page/tempate_filter';
    const XML_NODE_BLOCK_TEMPLATE_FILTER    = 'global/cms/block/tempate_filter';
	
    /**
     * 
     * @return mixed
     */
    public function isEnabled(){
    	return Mage::getStoreConfig('ced_csmarketplace/general/cmspageactivation');
    }
	/**
	 * 
	 * Fetch All Websites and Stores ...
	 */
	public function getAllwebsites(){
		$VendorPageStore =array();
		if(Mage::app()->getRequest()->getParam('page_id') && Mage::app()->getRequest()->getParam('page_id')>0){
			$page_id = Mage::app()->getRequest()->getParam('page_id');
			$VendorId = Mage::getSingleton('customer/session')->getVendorId();
			
			$CmsPage = Mage::getModel('cscmspage/vendorcms')->getCollection()
									->addFieldToFilter('vendor_id',$VendorId)
									->addFieldToFilter('page_id',$page_id);
			
			foreach	($CmsPage as $cms){
				$VendorPageStore[] = $cms->getStoreId();	
			}			
		}else if(Mage::app()->getRequest()->getParam('block_id') && Mage::app()->getRequest()->getParam('block_id')>0){
			$block_id = Mage::app()->getRequest()->getParam('block_id');
			$VendorId = Mage::getSingleton('customer/session')->getVendorId();
			
			$CmsBlockPage = Mage::getModel('cscmspage/vendorblock')->getCollection()
									->addFieldToFilter('vendor_id',$VendorId)
									->addFieldToFilter('block_id',$block_id);
			
			foreach	($CmsBlockPage as $block){
				$VendorPageStore[] = $block->getStoreId();	
			}
		}						
		?>
		
		<select multiple="multiple" class=" form-control required-entry select multiselect" size="10" title="Store View" name="vcmspage[store][]" id="page_store_id">
			<option value="0" <?php if(in_array('0',$VendorPageStore)){?> selected='selected' <?php }?>>All Store Views</option>
			<?php foreach (Mage::app()->getWebsites() as $website) { ?>
					<optgroup label="<?php echo $website->getName(); ?>">
					</optgroup>
					<?php foreach ($website->getGroups() as $group) {?>
							<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $group->getName() ?>">
							<?php 
							$stores = $group->getStores();
							foreach ($stores as $store) { ?>
					        	<option value="<?php echo $store->getId()?>" <?php if(in_array($store->getId(),$VendorPageStore)){?> selected='selected' <?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $store->getName() ?></option>
					        		
					        <?php }?>
							</optgroup>
					<?php }?>
					
			<?php }	?>
			
		</select>
		<?php	
	}
	
 	/**
     * Retrieve Template processor for Page Content
     *
     * @return Varien_Filter_Template
     */
    public function getPageTemplateProcessor()
    {
        $model = (string)Mage::getConfig()->getNode(self::XML_NODE_PAGE_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }

    /**
     * Retrieve Template processor for Block Content
     *
     * @return Varien_Filter_Template
     */
    public function getBlockTemplateProcessor()
    {
        $model = (string)Mage::getConfig()->getNode(self::XML_NODE_BLOCK_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }
    /**
     * @return Ced_CsCmsPage_Model_Data
     */
    public function getVendorId(){
    	$vid = '';
    	if(Mage::app()->getRequest()->getParam('vid')>0){
			$vid = Mage::app()->getRequest()->getParam('vid');
		}
		/*elseif(Mage::getSingleton('customer/session')->getVendorId()>0){
			$vid = Mage::getSingleton('customer/session')->getVendorId();
		}*/
		return $vid;
    }
    
    /**
     * getVendor Shop Page Url
     *
     * @return Ced_CsCmsPage_Model_Data
     * 
     */
    
    public function getVendorShopUrl(){
    	$shopurl = Mage::getSingleton('customer/session')->getVendor()->getShopUrlKey();
		$shopurl = str_replace(Ced_CsMarketplace_Model_Vendor::VENDOR_SHOP_URL_SUFFIX,'',$shopurl);
    	return 'vendorshop/'.$shopurl.'/';
    }
    /**
     * @return Pending Approval Cms Page Count
     */
    public function getApprovalVendorCms(){
    	$VendorCms = Mage::getModel('cscmspage/cmspage')->getCollection()
    						->addFieldToFilter('is_approve','0');
    	
		return count($VendorCms);			
    	
    }
    /**
     * @return Pending Approval Block Page Count
     */
	public function getApprovalVendorBlock(){
    	
    	$VendorCms = Mage::getModel('cscmspage/block')->getCollection()
    						->addFieldToFilter('is_approve','0');
    		
    	return count($VendorCms);	
    }
}

?>