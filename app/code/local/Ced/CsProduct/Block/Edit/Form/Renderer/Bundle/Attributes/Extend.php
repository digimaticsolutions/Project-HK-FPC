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
 * Bundle Extended Attribures Block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Form_Renderer_Bundle_Attributes_Extend extends Ced_CsProduct_Block_Widget_Form_Renderer_Fieldset_Element
{
    const DYNAMIC = 0;
    const FIXED = 1;

    public function getElementHtml()
    {
        $elementHtml = parent::getElementHtml();

        $switchAttributeCode = $this->getAttribute()->getAttributeCode().'_type';
        $switchAttributeValue = $this->getProduct()->getData($switchAttributeCode);

        $html = '<select name="product[' . $switchAttributeCode . ']" id="' . $switchAttributeCode . '" type="select" class="required-entry select next-toinput"' . ($this->getProduct()->getId() && $this->getAttribute()->getAttributeCode() == 'price' || $this->getElement()->getReadonly() ? ' disabled="disabled"' : '') . '>
            <option value="">' . $this->__('-- Select --') . '</option>
            <option ' . ($switchAttributeValue == self::DYNAMIC ? 'selected' : '') . ' value="' . self::DYNAMIC . '">' . $this->__('Dynamic') . '</option>
            <option ' . ($switchAttributeValue == self::FIXED ? 'selected' : '') . ' value="' . self::FIXED . '">' . $this->__('Fixed') . '</option>
        </select>';

        $html .= '<span class="next-toselect">'.$elementHtml.'</span>';
        if ($this->getDisableChild() && !$this->getElement()->getReadonly()) {
            $html .= "<script type=\"text/javascript\">
                function " . $switchAttributeCode . "_change() {
                    if ($('" . $switchAttributeCode . "').value == '" . self::DYNAMIC . "') {
                        $('" . $this->getAttribute()->getAttributeCode() . "').disabled = true;
                        $('" . $this->getAttribute()->getAttributeCode() . "').value = '';
                        $('" . $this->getAttribute()->getAttributeCode() . "').removeClassName('required-entry');

                        if ($('dynamic-price-warrning')) {
                            $('dynamic-price-warrning').show();
                        }
                    } else {
                        $('" . $this->getAttribute()->getAttributeCode() . "').disabled = false;
                        $('" . $this->getAttribute()->getAttributeCode() . "').addClassName('required-entry');

                        if ($('dynamic-price-warrning')) {
                            $('dynamic-price-warrning').hide();
                        }
                    }
                }

                $('" . $switchAttributeCode . "').observe('change', " . $switchAttributeCode . "_change);
                " . $switchAttributeCode . "_change();
            </script>";
        }
        return $html;
    }

    public function getProduct()
    {
        if (!$this->getData('product')){
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
}
