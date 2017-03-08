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
class Ced_CsStorePickup_Block_Adminhtml_Stores_Edit extends
                    Mage_Adminhtml_Block_Widget_Form_Container{
   public function __construct()
   {
   
        parent::__construct();
        $this->_objectId = 'pickup_id';
        $this->_blockGroup = 'csstorepickup';
        $this->_controller = 'adminhtml_stores';
      
        
        
        $objId = $this->getRequest()->getParam($this->_objectId);
        //echo $this->_objectId; die("kfjg");
        if (! empty($objId)) {
        	$this->_addButton('delete', array(
        			'label'     => Mage::helper('adminhtml')->__('Delete'),
        			'class'     => 'delete',
        			'onclick'   => 'deleteConfirm(\''
        			. Mage::helper('core')->jsQuoteEscape(
        					Mage::helper('adminhtml')->__('Are you sure you want to do this?')
        			)
        			.'\', \''
        			. $this->getDeleteUrl()
        			. '\')',
        	));
        } 
        
        
     //   $this->_updateButton('save', 'label','save');
       // $this->_updateButton('delete', 'label', 'delete ');
    }
      
    public function getHeaderText()
    {
        if( Mage::registry('storepickup_data')&&Mage::registry('storepickup_data')->getPickupId())
         {
              return 'Add Store'.$this->htmlEscape(
              Mage::registry('storepickup_data')->getTitle()).'<br />';
         }
         else
         {
             return 'Add Stores';
         }
    }
}
