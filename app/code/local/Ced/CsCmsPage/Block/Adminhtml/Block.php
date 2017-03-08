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

class Ced_CsCmsPage_Block_Adminhtml_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set template
     */
	
	public function __construct()
	{ 
		$this->_controller = 'adminhtml_block';
	    $this->_blockGroup = 'cscmspage';
	    $this->_headerText = Mage::helper('cscmspage')->__('Manage Block Pages');
	    parent::__construct();
		$this->_removeButton('add');
	}
	
}
