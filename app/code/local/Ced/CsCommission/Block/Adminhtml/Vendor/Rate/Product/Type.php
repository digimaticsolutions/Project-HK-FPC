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
 
class Ced_CsCommission_Block_Adminhtml_Vendor_Rate_Product_Type extends Mage_Core_Block_Html_Select
{
	/**
	 * Set Input Name
	 */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
		$this->setExtraParams('style="width: 150px;"');
        if (!$this->getOptions()) {
			$types = Mage::getModel('catalog/product_type')->getOptionArray();
			$this->addOption('alltype', $this->__('All Product Types'));
			if(count($types)>0){
				foreach($types as $value=>$label){
					$this->addOption($value, addslashes($label));
				}
			}
        }
        return parent::_toHtml();
    }
}
