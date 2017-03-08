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
  * @package     Ced_CsProduct
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Producty Edit block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsProduct_Block_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit
{
    
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('csproduct/edit.phtml');
        $this->setId('product_edit');
    }
    
    protected function _prepareLayout()
    {
    	if (!$this->getRequest()->getParam('popup')) {
    		$this->setChild('back_button',
    				$this->getLayout()->createBlock('adminhtml/widget_button')
    				->setData(array(
    						'label'     => Mage::helper('catalog')->__('Back'),
    						'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
    						'class' => 'btn btn-info uptransform'
    				))
    		);
    	} else {
    		$this->setChild('back_button',
    				$this->getLayout()->createBlock('adminhtml/widget_button')
    				->setData(array(
    						'label'     => Mage::helper('catalog')->__('Close Window'),
    						'onclick'   => 'window.close()',
    						'class' => 'cancel btn btn-warning uptransform'
    				))
    		);
    	}
    
    	if (!$this->getProduct()->isReadonly()) {
    		$this->setChild('reset_button',
    				$this->getLayout()->createBlock('adminhtml/widget_button')
    				->setData(array(
    						'label'     => Mage::helper('catalog')->__('Reset'),
    						'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/*', array('_current'=>true)).'\')',
							'class'		=> 'btn btn-warning uptransform'
    				))
    		);
    
    		$this->setChild('save_button',
    				$this->getLayout()->createBlock('adminhtml/widget_button')
    				->setData(array(
    						'label'     => Mage::helper('catalog')->__('Save'),
    						'onclick'   => 'productForm.submit()',
    						'class' => 'btn btn-success uptransform'
    				))
    		);
    	}
    
    	if (!$this->getRequest()->getParam('popup')) {
    		if (!$this->getProduct()->isReadonly()) {
    			$this->setChild('save_and_edit_button',
    					$this->getLayout()->createBlock('adminhtml/widget_button')
    					->setData(array(
    							'label'     => Mage::helper('catalog')->__('Save and Continue Edit'),
    							'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
    							'class' => 'btn btn-success uptransform'
    					))
    			);
    		}
    		if ($this->getProduct()->isDeleteable()) {
    			$this->setChild('delete_button',
    					$this->getLayout()->createBlock('adminhtml/widget_button')
    					->setData(array(
    							'label'     => Mage::helper('catalog')->__('Delete'),
    							'onclick'   => 'confirmSetLocation(\''.Mage::helper('catalog')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
    							'class'  => 'btn btn-danger uptransform'
    					))
    			);
    		}
    
    	}
    
    	return $this;
    }
    
    public function getValidationUrl()
    {
    	return Mage::getUrl('*/*/validate', array('_current'=>true));
    }
    
    public function getSaveUrl()
    {
    	return Mage::getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
    
    public function getSaveAndContinueUrl()
    {
    	return Mage::getUrl('*/*/save', array(
    			'_current'   => true,
    			'back'       => 'edit',
    			'tab'        => '{{tab_id}}',
    			'active_tab' => null
    	));
    }
    
    public function getDeleteUrl()
    {
    	return Mage::getUrl('*/*/delete', array('_current'=>true));
    }
    
	
}
