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
class Ced_Productfaq_Block_Adminhtml_Tabs_Tabid extends Mage_Adminhtml_Block_Widget
{  
  /**
     * Setting template file
     *
     */
public function __construct()
    { 
        parent::__construct();
        $this->setTemplate('productfaq/newtab.phtml');
    }
    /**
     * Prepare layout for adding faqs
     *
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add New Question'),
                    'class' => 'add',
                    'id'    => 'add_new_defined_question'
                ))
        );
        $this->setChild('options_box',
            $this->getLayout()->createBlock('productfaq/adminhtml_tabs_question')
        );

        return parent::_prepareLayout();
    }
    /**
     * Add button html
     *
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    /**
     * Options box html
     *
     */
    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
}