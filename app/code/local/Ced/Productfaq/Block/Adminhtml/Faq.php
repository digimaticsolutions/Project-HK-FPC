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
  * @package     Ced_Productfaq
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Productfaq_Block_Adminhtml_Faq extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  /**
     * Modify header & button labels
     *
     */
    public function __construct()
    {
    	$this->_blockGroup = 'productfaq';
        $this->_controller = 'adminhtml_faq';
        $this->_headerText = Mage::helper('productfaq')->__('Product Faq Management');
        parent::__construct();
        $this->_removeButton('add');
    }
}
