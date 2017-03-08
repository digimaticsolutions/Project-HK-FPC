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

class Ced_CsCmsPage_Block_Page extends Mage_Core_Block_Abstract
{
 	/**
     * Retrieve Page instance
     *
     * @return Ced_CsCmsPage_Model_Page
     */
    public function getPage()
    {
    	if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                $page = Mage::getModel('cscmspage/cmspage')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($this->getPageId(), 'identifier');
            } else {
                $page = Mage::getSingleton('cscmspage/cmspage');
            }
            //$this->addModelTags($page);
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    /**
     * Prepare global layout
     *
     * @return Ced_CsCmsPage_Block_Page
     */
    protected function _prepareLayout()
    {
    	$page = $this->getPage();
		// show breadcrumbs
        if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))
            && ($page->getIdentifier()!==Mage::getStoreConfig('web/default/cms_home_page'))
            && ($page->getIdentifier()!==Mage::getStoreConfig('web/default/cms_no_route'))) {
               // $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
                $breadcrumbs->addCrumb('cscmspage_page', array('label'=>$page->getTitle(), 'title'=>$page->getTitle()));
        }

        $root = $this->getLayout()->getBlock('root');
        if ($root) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare HTML content
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $helper Mage_Cms_Helper_Data */
        $helper = Mage::helper('cscmspage');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($this->getPage()->getContent());
        $html = $this->getMessagesBlock()->toHtml() . $html;
        return $html;
    }
}