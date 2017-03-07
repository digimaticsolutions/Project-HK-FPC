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
 * Create product settings tab
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Class constructor
	 *
	 */
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('csmarketplace/widget/form.phtml');
		$this->setDestElementId('edit_form');
		$this->setShowGlobalIcon(false);
	}
	
	/**
	 * Preparing global layout
	 *
	 * You can redefine this method in child classes for changin layout
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','attribute_set_id','product_type')",
                    'class'     => 'save'
                    ))
                );
        parent::_prepareLayout();
        Varien_Data_Form::setElementRenderer(
        $this->getLayout()->createBlock('csmarketplace/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
        $this->getLayout()->createBlock('csproduct/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
        $this->getLayout()->createBlock('csproduct/widget_form_renderer_fieldset_element')
        );
    }
	
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('csmarketplace')->__('Select')." ".Mage::helper('csmarketplace')->__('Product Type')));

        $entityType = Mage::registry('product')->getResource()->getEntityType();

        $fieldset->addField('attribute_set_id', 'select', array(
            'label' => Mage::helper('catalog')->__('Attribute Set'),
            'title' => Mage::helper('catalog')->__('Attribute Set'),
            'name'  => 'set',
            'value' => $entityType->getDefaultAttributeSetId(),
        	'required'=>true,
            'values'=> Mage::getModel('csmarketplace/system_config_source_vproducts_set')->toOptionArray(false,true)
        ));

        $fieldset->addField('product_type', 'select', array(
            'label' => Mage::helper('catalog')->__('Product Type'),
            'title' => Mage::helper('catalog')->__('Product Type'),
            'name'  => 'type',
            'value' => '',
        	'required'=>true,
            'values'=>Mage::getModel('csmarketplace/system_config_source_vproducts_type')->toOptionArray(false,true),
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return Mage::getUrl('*/*/new', array(
            '_current'  => true,
            'set'       => '{{attribute_set}}',
            'type'      => '{{type}}'
        ));
    }
}
