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
 * @package     Ced_CsCommission
 * @author   	CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_CsCommission_Block_Adminhtml_Vendor_Rate_Category extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_defaultRenderer;
    protected $_actionRenderer;
    protected $_priceTypeRenderer;

    /**
     * @return Categories 
     */
	protected function _getCategoryWiseRenderer()
    {
        if (!$this->_actionRenderer) {
            $this->_actionRenderer = $this->getLayout()->createBlock(
                'cscommission/adminhtml_vendor_rate_category_item', '',
                array('is_render_to_js_template' => true)
            );
            $this->_actionRenderer->setExtraParams('style="width:90px"');
        }
        return $this->_actionRenderer;
    }
	
    /**
     * @return Calculation Methods
     */
    protected function _getCalculationMethodRenderer()
    {
        if (!$this->_defaultRenderer) {
            $this->_defaultRenderer = $this->getLayout()->createBlock(
                'cscommission/adminhtml_vendor_rate_method', '',
                array('is_render_to_js_template' => true)
            );
            $this->_defaultRenderer->setExtraParams('style="width:60px"');
        }
        return $this->_defaultRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('category', array(
            'label' => Mage::helper('cscommission')->__('Category'),
			'renderer' => $this->_getCategoryWiseRenderer(),
        ));

        $this->addColumn('method', array(
            'label' => Mage::helper('cscommission')->__('Calculation Method'),
			'renderer' => $this->_getCalculationMethodRenderer(),
        ));

        $this->addColumn('fee', array(
            'label' => Mage::helper('customer')->__('Commission Fee'),
            'style' => 'width: 123px;',
        ));
		
		$this->addColumn('priority', array(
            'label' => Mage::helper('customer')->__('Priority'),
			'style' => 'width:53px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('cscommission')->__('Add New Rate');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getCategoryWiseRenderer()->calcOptionHash($row->getData('category')),
            'selected="selected"'
        );
		$row->setData(
            'option_extra_attr_' . $this->_getCalculationMethodRenderer()->calcOptionHash($row->getData('method')),
            'selected="selected"'
        );
        //Zend_Debug::dump($row, '_prepareArrayRow', true);
    }

	 /**
     * Get the grid and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
		$html .= '<input type="hidden" name="category_dummy" id="'.$element->getHtmlId().'" />';
        return $html;
    }
}
