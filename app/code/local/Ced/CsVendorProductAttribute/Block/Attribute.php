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
 * Producty Edit block
 *
 * @category   Ced
 * @package    Ced_CsVendorProductAttribute
 */

class Ced_CsVendorProductAttribute_Block_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Prepare button and grid 
	 */
	public function __construct()
	{
	    $newurl=Mage::getUrl('csvendorproductattribute/attribute/new');
		$this->_controller = 'attribute';
		$this->_blockGroup = 'csvendorproductattribute';
		$this->_headerText = Mage::helper('catalog')->__('Manage Your Attributes');
		$this->_addButtonLabel = Mage::helper('catalog')->__('Create Attribute');
		parent::__construct();
		$this->_removeButton('add');
		$this->_addButton('add', array(
				'label'     => $this->_addButtonLabel,
				'onclick' => "setLocation('{$newurl}')",
				'class'     => 'btn btn-primary uptransform',
		));
	   
	}

}
