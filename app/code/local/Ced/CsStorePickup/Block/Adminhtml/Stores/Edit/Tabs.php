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
class Ced_CsStorePickup_Block_Adminhtml_Stores_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('posts_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Pickup Information'));
        
    }


     protected function _beforeToHtml() {
     	
    $this->addTab('Store_Information', array(
				'label' => 'Store Information',
				'title' => 'Store Information',
				'content' => $this->getLayout()->createBlock('storepickup/adminhtml_store_edit_tab_main')->toHtml()
		));

 $this->addTab('Store_Hour_Information', array(
		'label'     => 'Store Hour Information ',
 		'title' => 'Store Hour Information',
		'content'   => $this->getLayout()->createBlock('storepickup/adminhtml_store_edit_tab_storehour')->setTemplate('storepick/storehour.phtml')->toHtml()
)); 
    	return parent::_beforeToHtml();
    } 

    
}
