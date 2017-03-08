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
 * Attribute Edit block
 *
 * @category   Ced
 * @package    Ced_CsVendorProductAttribute
 */
class Ced_CsVendorProductAttribute_Block_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected $_objectId = 'id';
    
  public function __construct()
    {
    	
        $this->_objectId = 'attribute_id';
	    $this->_blockGroup = 'csvendorproductattribute';
        $this->_controller = 'attribute';
        $newurl=Mage::getUrl('csvendorproductattribute/attribute/index');
        parent::__construct();
        if($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label'     => Mage::helper('catalog')->__('Close Window'),
                    'class'     => 'cancel',
                    'onclick'   => 'window.close()',
                    'level'     => -1
                )
            );
        } else {
           
            $this->_addButton(
                'save_and_edit_button',
                array(
                    'label'     => Mage::helper('catalog')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                    'class'     => 'btn btn-success uptransform'
                ),
                100
            );
        }
       
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_addButton('back', array(
        		'label'     => Mage::helper('catalog')->__('Back'),
        		'onclick'   => "setLocation('{$newurl}')",
        		'class'     => 'btn btn-info uptransform',
        ), -1);
        
        $this->_addButton('reset', array(
        		'label'     => Mage::helper('catalog')->__('Reset'),
        		'onclick'   => 'setLocation(window.location.href)',
        		'class'     => 'btn btn-warning uptransform',
        ), -1);
        $this->_addButton('save', array(
        		'label'     => Mage::helper('catalog')->__('Save Attribute'),
        		'onclick'   => 'editForm.submit()',
        		'class'     => 'btn btn-success uptransform',
        ), 1);
        
        $objId = $this->getRequest()->getParam($this->_objectId);
        
        if (! empty($objId)) {
        	$this->_addButton('delete', array(
        			'label'     => Mage::helper('catalog')->__('Delete Attribute'),
        			'class'     => 'btn btn-danger uptransform',
        			'onclick'   => 'deleteConfirm(\''. Mage::helper('catalog')->__('Are you sure you want to do this?')
        			.'\', \'' . $this->getDeleteUrl() . '\')',
        	));
        }
       
    }
  
  
    public function getHeaderText()
    {
     if (Mage::registry('entity_attribute')->getId()) {
    		$frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
    		if (is_array($frontendLabel)) {
    			$frontendLabel = $frontendLabel[0];
    		}
    		return Mage::helper('catalog')->__('Edit Product Attribute "%s"', $this->escapeHtml($frontendLabel));
    	}
    	else {
    		return Mage::helper('catalog')->__('New Product Attribute');
    	}
    }
    
    public function getValidationUrl()
    {
    	return Mage::getUrl('csvendorproductattribute/attribute/validate', array('_current'=>true));
    }
    
    public function getSaveUrl()
    {
    
    	return Mage::getUrl('csvendorproductattribute/attribute/save', array('_current'=>true, 'back'=>null));
    }
    public function getDeleteUrl()
    {
    	return Mage::getUrl('csvendorproductattribute/attribute/delete', array('_current'=>true));
    }
    public function getSaveAndContinueUrl()
    {
    	return Mage::getUrl('csvendorproductattribute/attribute/save', array(
    			'_current'   => true,
    			'back'       => 'edit',
    			'tab'        => '{{tab_id}}',
    			
    	));
    }
    
}
