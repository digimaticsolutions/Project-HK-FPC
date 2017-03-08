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
  * @package     Ced_CsProduct
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Product Edit tabs block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsProduct_Block_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'csproduct/edit_tab_attributes';
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('csproduct')->__('Product Info'));
    //    $this->setTemplate('csproduct/widget/tabs.phtml');
    }
	
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changin layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId);
            if(version_compare(Mage::getVersion(), '1.6', '<')) {
            	$groupCollection->getSelect()->order('main_table.sort_order');
            }
            else{
            	$groupCollection->setSortOrder()
            	->load();
            }
            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add groups without attributes

                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() ) {
                        unset($attributes[$key]);
                    }
                    if($attribute->getAttributeCode()=="status"){
                    	$vmodel=Mage::getModel('csmarketplace/vproducts');
                    	if($vmodel->isProductApprovalRequired() && $this->getRequest()->getActionName()=="new"){
                    		unset($attributes[$key]);
                    	}
                    	else if($this->getRequest()->getActionName()=="edit"){
                    		$vmodel=$vmodel->loadByField(array('product_id'),array($product->getId()));
                    		if($vmodel && $vmodel->getId()){
                    			if($vmodel->getCheckStatus()!=Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS){
                    				unset($attributes[$key]);
                    			}
                    		}
                    	
                    	}
                    	
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }

                $this->addTab('group_'.$group->getId(), array(
                    'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock())
                            ->setGroup($group)
                            ->setGroupAttributes($attributes)
                            ->toHtml()),
                ));
            }
        	if (Mage::helper('core')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', array(
                    'label'     => Mage::helper('catalog')->__('Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_product_edit_tab_inventory')->setTemplate('csproduct/edit/tab/inventory.phtml')->toHtml()),
                ));
            }
            
            /**
             * Don't display website tab for single mode
             */
            if (Mage::helper('csmarketplace')->isSharingEnabled() && !Mage::app()->isSingleStoreMode()) {
            	$this->addTab('websites', array(
            			'label'     => Mage::helper('catalog')->__('Websites'),
            			'content'   => $this->_translateHtml($this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_websites')->setTemplate('csproduct/edit/tab/websites.phtml')->toHtml()),
            	));
            }
            
            $this->addTab('categories',  array(
                    'label'     => Mage::helper('catalog')->__('Categories'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('adminhtml/catalog_form')->setTemplate('csproduct/edit/tab/categories.phtml')->toHtml()),
             ));
            
            if(Mage::getStoreConfig('ced_vproducts/general/relatedproducts',$product->getStoreId())){
	            $this->addTab('related', array(
	            		'label'     => Mage::helper('catalog')->__('Related Products'),
	            		'url'       => Mage::getUrl('*/*/related', array('_current' => true)),
	            		'class'     => 'ajax',
	            ));
            }
            
            if(Mage::getStoreConfig('ced_vproducts/general/upsells',$product->getStoreId())){
	            $this->addTab('upsell', array(
	            		'label'     => Mage::helper('catalog')->__('Up-sells'),
	            		'url'       => Mage::getUrl('*/*/upsell', array('_current' => true)),
	            		'class'     => 'ajax',
	            ));
            }
            
            if(Mage::getStoreConfig('ced_vproducts/general/crosssells',$product->getStoreId())){
	            $this->addTab('crosssell', array(
	            		'label'     => Mage::helper('catalog')->__('Cross-sells'),
	            		'url'       => Mage::getUrl('*/*/crosssell', array('_current' => true)),
	            		'class'     => 'ajax',
	            ));
            }
            
            /**
             * Do not change this tab id
             * @see Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
             * @see Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs
             */
            if (!$product->isGrouped() && Mage::getStoreConfig('ced_vproducts/general/customoptions',$product->getStoreId())) {
                $this->addTab('customer_options', array(
                    'label' => Mage::helper('catalog')->__('Custom Options'),
                    'url'   => Mage::getUrl('*/*/options', array('_current' => true)),
                    'class' => 'ajax',
                ));
            }

        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('catalog')->__('Product Type'),
                'content'   => $this->_translateHtml($this->getLayout()->createBlock('csproduct/edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    { 
        if (!($this->getData('product') instanceof Mage_Catalog_Model_Product)) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Translate html content
     * 
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
