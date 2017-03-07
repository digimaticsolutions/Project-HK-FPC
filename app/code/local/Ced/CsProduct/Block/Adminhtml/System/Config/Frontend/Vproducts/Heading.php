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
 
class Ced_CsProduct_Block_Adminhtml_System_Config_Frontend_Vproducts_Heading extends Mage_Adminhtml_Block_System_Config_Form_Field_Heading
{
	
	/**
	 * Render element html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$useContainerId = $element->getData('use_container_id');
		$html='';
		$html.=sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
		$element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
		);
		$html.='<script>
				var enable=document.getElementById("ced_csmarketplace_general_activation_vproducts").value;
				if(enable==0){
					document.getElementById("row_'.$element->getHtmlId().'").style.display="none";
				}
				</script>';
		return $html;
	}
	
	   
}
