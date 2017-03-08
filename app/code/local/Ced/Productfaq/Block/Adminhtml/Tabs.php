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
class Ced_Productfaq_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{

    private $parent;

    /**
     * Adding tab to product edit page 
     *
     */
    protected function _prepareLayout()
    { 
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        //add new tab
        $enable = Mage::getStoreConfig('productfaq/general/enable');
        if($enable=='1')
        {
        $this->addTab('tabid', array(
                     'label'     => Mage::helper('catalog')->__('FAQS'),
                 	   'url'   => $this->getUrl('*/*/faqs', array('_current' => true)),
        		         'class' => 'ajax',
        ));
        }
        return $this->parent;
    }
}
