<?php 
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced;
 * @package     Ced_CsOrder 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* @var $installer Ced_CsOrder_Model_Entity_Setup */
	$installer=Mage::getModel('eav/entity_setup','core_setup');
	
	$installer->addAttribute('customer_address', 'vendor_id', array(
		'type' => 'varchar',
		'input' => 'hidden',
		'label' => 'Vendor Id',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 0
	));
	 Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'vendor_id')
		->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
		->save(); 
	$installer->addAttribute('customer_address', 'vendor_reference', array(
		'type' => 'varchar',
		'input' => 'hidden',
		'label' => 'Vendor Reference',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 0
	));
	Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'vendor_reference')
		->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
		->save();	
	
			
			 
	$installer->addAttribute('customer_address', 'for_vendor', array(
		'type' => 'varchar',
		'input' => 'hidden',
		'label' => 'For Vendor',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 0,
		'default'=>0
	));
	 Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'for_vendor')
		->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
		->save();
			 