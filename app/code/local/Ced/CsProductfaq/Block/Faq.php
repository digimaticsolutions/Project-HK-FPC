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
 * @package     Ced_CsProductfaq
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsProductfaq_Block_Faq extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Set template
     */
    public function __construct()
    {

        parent::__construct();
        $this->setTemplate('productfaq/faq.phtml');
    }

    /**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
    	$newurl=Mage::getUrl('*/*/new');
    	$this->_addButton('add_new', array(
            'label'   => Mage::helper('catalog')->__('Add Faq'),
            'onclick' => "setLocation('{$newurl}')",
            'class'   => 'btn btn-primary uptransform'
        ));

        $this->setChild('grid', $this->getLayout()->createBlock('csproductfaq/grid', 'productfaq.grid'));
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
    
    
    public function getfaqValues($vid)
    {
    	$collection= Mage::getModel ('csmarketplace/vproducts')->getCollection()->addFieldToFilter('check_status',array('nin'=>3)
    	)->addFieldToFilter('vendor_id',$vid);
    	
    	foreach($collection as $key => $value){
    		$arr[$value->getProductId()] = $value->getName();
    	}
    	foreach ($arr as $key=>$val){
    		$options[]= array('value'=>$key, 'label'=>$val);
    	}
    
    	return $options;
    }
}