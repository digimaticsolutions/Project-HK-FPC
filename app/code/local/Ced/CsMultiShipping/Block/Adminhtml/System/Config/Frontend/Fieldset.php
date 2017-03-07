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
 
class Ced_CsMultiShipping_Block_Adminhtml_System_Config_Frontend_Fieldset
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        if($websitecode=Mage::app()->getRequest()->getParam('website')){
        	$website = Mage::getModel('core/website')->load($websitecode);
        	if($website && $website->getWebsiteId()){
        		$active = $website->getConfig('ced_csmultishipping/general/activation')?1:0;
        	}
        }
        else
        	$active = Mage::getStoreConfig('ced_csmultishipping/general/activation')?1:0;
        
        $validation = $active ?0:1;
        foreach ($element->getSortedElements() as $field) {
            $html.= $field->toHtml();
        }
		
        $html .= $this->_getFooterHtml($element);
        $html.='<script>
        		var enable=0;
				
				if('.$validation.'){
					document.getElementById("'.$element->getHtmlId().'").style.display="none";
					document.getElementById("'.$element->getHtmlId().'-state").previousElementSibling.style.display="none";
					document.getElementById("'.$element->getHtmlId().'-state").style.display="none";
				}
				</script>';
        return $html;
    }
}
