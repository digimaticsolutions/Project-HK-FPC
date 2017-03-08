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
 * edit tabs for configurable products
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsProduct_Block_Edit_Tabs_Configurable extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('product_info_tabs');
		$this->setDestElementId('product_edit_form');
		$this->setTitle(Mage::helper('csproduct')->__('Product Info'));
		$this->setTemplate('csmarketplace/widget/tabs.phtml');
	}
	
	protected function _prepareLayout()
    {
//        $product = $this->getProduct();

//        if (!($superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds())) {
            $this->addTab('super_settings', array(
                'label'     => Mage::helper('catalog')->__('Configurable Product Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_settings')->toHtml(),
                'active'    => true
            ));
            
//        } else {
//            parent::_prepareLayout();
//
//            $this->addTab('configurable', array(
//                'label'     => Mage::helper('catalog')->__('Associated Products'),
//                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config', 'admin.super.config.product')
//                    ->setProductId($this->getRequest()->getParam('id'))
//                    ->toHtml(),
//            ));
//            $this->bindShadowTabs('configurable', 'customer_options');
//        }
    } 
}
