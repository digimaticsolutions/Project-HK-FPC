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

class Ced_CsCmsPage_Helper_Cmspage extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NO_ROUTE_PAGE        = 'web/default/cms_no_route';
    const XML_PATH_NO_COOKIES_PAGE      = 'web/default/cms_no_cookies';
    const XML_PATH_HOME_PAGE            = 'web/default/cms_home_page';

    /**
    * Renders CMS page on front end
    *
    * Call from controller action
    *
    * @param Mage_Core_Controller_Front_Action $action
    * @param integer $pageId
    * @return boolean
    */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $pageId = null)
    {
    	
    	return $this->_renderPage($action, $pageId);
    }

   /**
    * Renders CMS page
    *
    * @param Mage_Core_Controller_Front_Action $action
    * @param integer $pageId
    * @param bool $renderLayout
    * @return boolean
    */
    protected function _renderPagesxcdscs(Mage_Core_Controller_Varien_Action  $action, $pageId = null, $renderLayout = true)
    {
    	
	  try{
		   	$page = Mage::getSingleton('cscmspage/cmspage');
		   	 
		   	if (!is_null($pageId) && $pageId!==$page->getId()) {
		   		 
		   		$delimeterPosition = strrpos($pageId, '|');
		   		if ($delimeterPosition) {
		   			$pageId = substr($pageId, 0, $delimeterPosition);
		   		}
		   	
		   		$page->setStoreId(Mage::app()->getStore()->getId());
		   		if (!$page->load($pageId)) {
		   			return false;
		   		}
		   	}
		   	 
		   	if (!$page->getId()) {
		   		return false;
		   	}
		   	 
		   	$inRange = Mage::app()->getLocale()
		   	->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo());
		   	
		   	if ($page->getCustomTheme()) {
		   		if ($inRange) {
		   			list($package, $theme) = explode('/', $page->getCustomTheme());
		   			Mage::getSingleton('core/design_package')
		   			->setPackageName($package)
		   			->setTheme($theme);
		   		}
		   	}else {
		   		$package = Mage::getSingleton('core/design_package')->getPackageName();
		   		$theme = Mage::getSingleton('core/design_package')->getTheme('frontend');
		   		Mage::getSingleton('core/design_package')
		   		->setPackageName($package)
		   		->setTheme($theme);
		   	}
		   	
		   	$action->getLayout()->getUpdate()
		   	->addHandle('default')
		   	->addHandle('cscmspage_page_index');
		   	$action->addActionLayoutHandles();
		   	if ($page->getRootTemplate()) {
		   		$handle = ($page->getCustomRootTemplate()
		   				&& $page->getCustomRootTemplate() != 'empty'
		   				&& $inRange) ? $page->getCustomRootTemplate() : $page->getRootTemplate();
		   		$action->getLayout()->helper('page/layout')->applyHandle($handle);
		   	}
		   	 
		   	Mage::dispatchEvent('cms_page_render', array('page' => $page, 'controller_action' => $action));
		   	 
		   	$action->loadLayoutUpdates();
		   	
		   	$layoutUpdate = ($page->getCustomLayoutUpdateXml() && $inRange)
		   	? $page->getCustomLayoutUpdateXml() : $page->getLayoutUpdateXml();
		   	$action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
		   	$action->generateLayoutXml()->generateLayoutBlocks();
		   	
		   	$contentHeadingBlock = $action->getLayout()->getBlock('page_content_heading');
		   	if ($contentHeadingBlock) {
		   		$contentHeading = $this->escapeHtml($page->getContentHeading());
		   		$contentHeadingBlock->setContentHeading($contentHeading);
		   	}
		   	 
		   	if ($page->getRootTemplate()) {
		   		$action->getLayout()->helper('page/layout')
		   		->applyTemplate($page->getRootTemplate());
		   	}
		   	
		   	$messageBlock = $action->getLayout()->getMessagesBlock();
		   	foreach (array('catalog/session', 'checkout/session', 'customer/session') as $storageType) {
		   		$storage = Mage::getSingleton($storageType);
		   		if ($storage) {
		   			$messageBlock->addStorageType($storageType);
		   			$messageBlock->addMessages($storage->getMessages(true));
		   		}
		   	}
		   	
		   	if ($renderLayout) {
		   		$action->renderLayout();
		   	}
		   	
		   	return true;
		}
		catch(Exception $e)
		{
			die($e);
		}
       
    }

    protected function _renderPage(Mage_Core_Controller_Varien_Action  $action, $pageId = null, $renderLayout = true)
    {
    
    
    	$page = Mage::getSingleton('cscmspage/cmspage');
    
    	if (!is_null($pageId) && $pageId!==$page->getId()) {
    		$delimeterPosition = strrpos($pageId, '|');
    		if ($delimeterPosition) {
    			$pageId = substr($pageId, 0, $delimeterPosition);
    		}
    
    		$page->setStoreId(Mage::app()->getStore()->getId());
    		if (!$page->load($pageId)) {
    			return false;
    		}
    	}
    
    	if (!$page->getId()) {
    		return false;
    	}
    
    	$inRange = Mage::app()->getLocale()
    	->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo());
    
    	if ($page->getCustomTheme()) {
		   		if ($inRange) {
		   			list($package, $theme) = explode('/', $page->getCustomTheme());
		   			Mage::getSingleton('core/design_package')
		   			->setPackageName($package)
		   			->setTheme($theme);
		   		}
		   	}else {
		   		$package = Mage::getSingleton('core/design_package')->getPackageName();
		   		$theme = Mage::getSingleton('core/design_package')->getTheme('frontend');
		   		Mage::getSingleton('core/design_package')
		   		->setPackageName($package)
		   		->setTheme($theme);
		   	}
    	 
//     	$package = Mage::getSingleton('core/design_package')->getPackageName();
//     	$theme = Mage::getSingleton('core/design_package')->getTheme('frontend');
    	
//     	Mage::getSingleton('core/design_package')
//     	->setPackageName($package)
//     	->setTheme($theme);
    
    	$action->getLayout()->getUpdate()
    	->addHandle('default')
    	->addHandle('cscmspage_page');
    	$action->addActionLayoutHandles();
    	if ($page->getRootTemplate()) {
    		$handle = ($page->getCustomRootTemplate()
    				&& $page->getCustomRootTemplate() != 'empty'
    				&& $inRange) ? $page->getCustomRootTemplate() : $page->getRootTemplate();
    		$action->getLayout()->helper('page/layout')->applyHandle($handle);
    	}
    
    	Mage::dispatchEvent('cms_page_render', array('page' => $page, 'controller_action' => $action));
    
    	$action->loadLayoutUpdates();
    	$layoutUpdate = ($page->getCustomLayoutUpdateXml() && $inRange)
    	? $page->getCustomLayoutUpdateXml() : $page->getLayoutUpdateXml();
    	$action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
    	$action->generateLayoutXml()->generateLayoutBlocks();
    
    	$contentHeadingBlock = $action->getLayout()->getBlock('page_content_heading');
    	if ($contentHeadingBlock) {
    		$contentHeading = $this->escapeHtml($page->getContentHeading());
    		$contentHeadingBlock->setContentHeading($contentHeading);
    	}
    
    	if ($page->getRootTemplate()) {
    		$action->getLayout()->helper('page/layout')
    		->applyTemplate($page->getRootTemplate());
    	}
    
    	$messageBlock = $action->getLayout()->getMessagesBlock();
    	foreach (array('catalog/session', 'checkout/session', 'customer/session') as $storageType) {
    		$storage = Mage::getSingleton($storageType);
    		if ($storage) {
    			$messageBlock->addStorageType($storageType);
    			$messageBlock->addMessages($storage->getMessages(true));
    		}
    	}
    
    	if ($renderLayout) {
    		$action->renderLayout();
    	}
    
    	return true;
    }
    
    
    
    
    
    
    
    
    
    
    /**
     * Renders CMS Page with more flexibility then original renderPage function.
     * Allows to use also backend action as first parameter.
     * Also takes third parameter which allows not run renderLayout method.
     *
     * @param Mage_Core_Controller_Varien_Action $action
     * @param $pageId
     * @param $renderLayout
     * @return bool
     */
    public function renderPageExtended(Mage_Core_Controller_Varien_Action $action, $pageId = null, $renderLayout = true)
    {
        return $this->_renderPage($action, $pageId, $renderLayout);
    }

    /**
     * Retrieve page direct URL
     *
     * @param string $pageId
     * @return string
     */
    public function getPageUrl($pageId = null)
    {
        $page = Mage::getModel('cscmspage/cmspage');
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return null;
            }
        }

        if (!$page->getId()) {
            return null;
        }

        return Mage::getUrl(null, array('_direct' => $page->getIdentifier()));
    }
}
