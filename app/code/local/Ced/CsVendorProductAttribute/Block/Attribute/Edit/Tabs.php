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
  * @package     Ced_CsVendorProductAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Vendor-Product-Attributes attributes tab
 *
 * @category   Ced
 * @package    Ced_CsVendorProductAttribute
 * @author 	   CedCommerce Core Team <connect@cedcommerce.com>
 */

class Ced_CsVendorProductAttribute_Block_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Set the template for tabs
	 */
	
   public function __construct()
	{  
		parent::__construct();
        $this->setId('product_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Attribute Information'));
        $this->setTemplate('csattribute/edit/tabs.phtml');
	}
	protected function _prepareLayout()
	{
		$this->addTab('main', array(
				'label'     => Mage::helper('catalog')->__('Properties'),
				'title'     => Mage::helper('catalog')->__('Properties'),
				'content'   => $this->getLayout()->createBlock('csvendorproductattribute/attribute_edit_tab_main')->toHtml(),
				'active'    => true
		));
		
		$model = Mage::registry('entity_attribute');
		
		$this->addTab('labels', array(
				'label'     => Mage::helper('catalog')->__('Manage Label / Options'),
				'title'     => Mage::helper('catalog')->__('Manage Label / Options'),
				'content'   => $this->getLayout()->createBlock('csvendorproductattribute/attribute_edit_tab_options')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
	
}
