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
 
class Ced_CsProduct_Block_Adminhtml_System_Config_Frontend_Vproducts_Enable extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	
	/**
	 * Return element html
	 *
	 * @param  Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{	$html='';
		$html.=$element->getElementHtml();
		$html.='<script>
				var enable=document.getElementById("ced_csmarketplace_general_activation_vproducts").value;
				if(enable==0){
					document.getElementById("row_'.$element->getHtmlId().'").style.display="none";
				}
				</script>';
		return $html;
	}
	   
}
