<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMultiShipping
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsMultiShipping_Block_Adminhtml_System_Config_Frontend_Heading extends Mage_Adminhtml_Block_System_Config_Form_Field_Heading
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
		$active=1;
		if($websitecode=Mage::app()->getRequest()->getParam('website')){
			$website = Mage::getModel('core/website')->load($websitecode);
			if($website && $website->getWebsiteId()){
				$active = $website->getConfig('ced_csmultishipping/general/activation')?1:0;
			}
		}
		else
			$active = Mage::getStoreConfig('ced_csmultishipping/general/activation')?1:0;
		
		$methods = Mage::getModel('csmultishipping/source_shipping_methods')->getMethods();
		$count=0;
		if(count($methods)>0)
			$count=1;
		$validation = $active && $count?0:1;
		//var_dump($validation);die;
		$html='';
		$html.=sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
		$element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
		);
		$html.='<script>
				if('.$validation.'){
					document.getElementById("row_'.$element->getHtmlId().'").style.display="none";
				}
				</script>';
		return $html;
	}
	
	   
}