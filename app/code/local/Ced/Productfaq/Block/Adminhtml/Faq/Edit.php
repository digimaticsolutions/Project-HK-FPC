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
class Ced_Productfaq_Block_Adminhtml_Faq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Modify header & button labels
     *
     */
    public function __construct()
    {
      
        parent::__construct();
                  
        $this->_objectId = 'id';
        $this->_blockGroup = 'productfaq';
        $this->_controller = 'adminhtml_faq';
         
        $this->_updateButton('save', 'label', Mage::helper('productfaq')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('productfaq')->__('Delete'));
         
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('productfaq')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        $this->_formScripts[] = "
             function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
             }
             ";
    }
 
    /**
     * Setting Header Text
     *
     */
    public function getHeaderText()
    {
        return Mage::helper('productfaq')->__('Frequently Asked Questions');
    }
}
