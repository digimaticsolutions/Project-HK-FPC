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
  * @package     Ced_CsStorePickup
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsStorePickup_Block_Store extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('csstorepickup/storepickup.phtml');
    }

    /**
     * Prepare button and grid
     *
     * @return Ced_CsCmsPage_Block_Adminhtml_CsCmsPage_Cmspage
     */
    protected function _prepareLayout()
    {
    	$newurl=Mage::getUrl('*/*/new');
    	$this->_addButton('add_new', array(
            'label'   => Mage::helper('catalog')->__('Add New Store'),
            'onclick' => "setLocation('{$newurl}')",
            'class'   => 'add'
        ));

        $this->setChild('grid', $this->getLayout()->createBlock('csstorepickup/grid'));
        return parent::_prepareLayout();
    }

    /**
     * Deprecated since 1.3.2
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
       return $this->getChildHtml('add_new_button');
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
}
