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

class  Ced_CsProduct_Block_Widget_Form_Renderer_Fieldset_Element
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Initialize block template
     */
    protected function _construct()
    {
        $this->setTemplate('csproduct/widget/form/renderer/fieldset/element.phtml');
    }
    
    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @return string
     */
    public function getScopeLabel()
    {
    	$html = '';
    	$attribute = $this->getElement()->getEntityAttribute();
    	if (!$attribute || $attribute->getFrontendInput()=='gallery') {
    		return $html;
    	}
    
    	/*
    	 * Check if the current attribute is a 'price' attribute. If yes, check
    	 * the config setting 'Catalog Price Scope' and modify the scope label.
    	 */
    	$isGlobalPriceScope = false;
    	if ($attribute->getFrontendInput() == 'price') {
    		$priceScope = Mage::getStoreConfig('catalog/price/scope');
    		if ($priceScope == 0) {
    			$isGlobalPriceScope = true;
    		}
    	}
    
    	if ($attribute->isScopeGlobal() || $isGlobalPriceScope) {
    		$html .= Mage::helper('adminhtml')->__('[GLOBAL]');
    	} elseif ($attribute->isScopeWebsite()) {
    		$html .= Mage::helper('adminhtml')->__('[WEBSITE]');
    	} elseif ($attribute->isScopeStore()) {
    		$html .= Mage::helper('adminhtml')->__('[STORE VIEW]');
    	}
    
    	return $html;
    }
}
