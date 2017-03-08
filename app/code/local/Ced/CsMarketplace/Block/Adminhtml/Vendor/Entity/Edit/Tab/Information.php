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
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit_Tab_Information extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){ 	
		$form = new Varien_Data_Form(); 
		$this->setForm($form);
		$model = Mage::registry('vendor_data')->getData();
		$vendor_id = $this->getRequest()->getParam('vendor_id',0);
		$vattribute_enabled = FALSE;
		if(Mage::helper('core')->isModuleEnabled('Ced_CsVAttribute')) {
            $vattribute_enabled = TRUE;
        }
        $_helper = Mage::helper('csmarketplace');
		
		$group = $this->getGroup();
		$attributeCollection = $this->getGroupAttributes();
		
		$fieldset = $form->addFieldset('group_'.$group->getId(), array('legend'=>Mage::helper('csmarketplace')->__($group->getAttributeGroupName())));    
		
		foreach($attributeCollection as $attribute){
			$ascn = 0;
			if ($attribute->getAttributeCode() !='customer_id' && (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible()))) {
				continue;
			}
			if (/* ($attribute->getAttributeCode()=="email" || */ $attribute->getAttributeCode()=="website_id") {
				continue;
			}
			
			if(!$this->getRequest()->getParam('vendor_id')){
				if ($attribute->getAttributeCode()=="member_id"){
					continue;
				}
			}
			
			if (!Mage::getStoreConfig('ced_csmarketplace/general/shopurl_active'))
			{
				if ($attribute->getAttributeCode()=="shop_url"){
					continue;
				}
			}
			if (!Mage::getStoreConfig('ced_csmarketplace/general/publicname_active'))
			{
				if ($attribute->getAttributeCode()=="public_name"){
					continue;
				}
			}
			
			
			if ($inputType = $attribute->getFrontend()->getInputType()) {
				if($vendor_id && $attribute->getAttributeCode()=="created_at") {
					$inputType = 'label';
				} elseif (!$vendor_id && $attribute->getAttributeCode()=="created_at") {
					continue;
				}
				if(!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])){ $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();  }
				 
				$showNewStatus = false;
				if($inputType == 'boolean') $inputType = 'select';
				if($attribute->getAttributeCode() == 'customer_id' && $vendor_id) {
					$options = $attribute->getSource()->toOptionArray($model[$attribute->getAttributeCode()]);
					if(count($options)) {
						$ascn = isset($options[0]['label'])?$options[0]['label']:0;
					}
				}
				if($attribute->getAttributeCode() == 'status' && !$vendor_id) {
					$showNewStatus = true;	
				}
				
				
				$fieldType      = $inputType;
				
				$rendererClass  = $attribute->getFrontend()->getInputRendererClass();
				if (!empty($rendererClass)) {
					$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
					$form->addType($fieldType, $rendererClass);
				}

				$label = $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel();
                $label = $vattribute_enabled?$label:$_helper->__($label);	
				
				$element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
					array(
						'name'      => "vendor[".$attribute->getAttributeCode()."]",
						'label'     => $label,
						'class'     => $attribute->getFrontend()->getClass(),
						'required'  => $attribute->getIsRequired(),
						'note'      => $ascn && $attribute->getAttributeCode() == 'customer_id' && $vendor_id?'':$attribute->getNote(),
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'disabled':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?true:'',
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'readonly':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?true:'',
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'style':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'display: none;':'',
						'value'    => $model[$attribute->getAttributeCode()],
					)
				)
				->setEntityAttribute($attribute);
				if ($attribute->getAttributeCode() == 'shop_url') {
					$element->setAfterElementHtml('<style type="text/css">
														span.ced-csmarketplace-availability-failed{
															background : url("'.Mage::getBaseUrl('skin').'frontend/base/default/images/ced/csmarketplace/error_msg_icon.gif");
															display: block;
															height: 17px;
															width: 16px;
															margin: 6px;
														}
														span.ced-csmarketplace-availability-passed {
															background : url("'.Mage::getBaseUrl('skin').'frontend/base/default/images/ced/csmarketplace/success_msg_icon.gif");
															display: block;
															height: 17px;
															width: 16px;
															margin: 6px;
														}
													</style>
													</td><td><span id="ced-csmarketplace-availability">&nbsp;</span>'
												  );
				} elseif($ascn && $attribute->getAttributeCode() == 'customer_id' && $vendor_id) {
					$element->setAfterElementHtml('<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit',array('id'=>$model[$attribute->getAttributeCode()], '_secure'=>true)).'" title="'.$ascn.'">'.$ascn.'</a>');
				}
				else if($attribute->getAttributeCode() == 'customer_id') {
					$element->setAfterElementHtml('<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/customer/new').'" title="Create New Customer">Create New Customer</a>');
				}
				else if($element->getExtType() == 'file') {
				    if ($element->getValue()) {
					$url = Mage::getBaseUrl('media').$element->getValue();
					$element->setAfterElementHtml('<p><a href="'.$url.'" target="_blank" >'.$element->getLabel().' Download</a></p>');
				    }
				}
				else {
					$element->setAfterElementHtml('');
				}
				if ($inputType == 'select') {
					$element->setValues($attribute->getSource()->getAllOptions(true,$showNewStatus));
				} else if ($inputType == 'multiselect') {
					$element->setValues($attribute->getSource()->getAllOptions(false,$showNewStatus));
					$element->setCanBeEmpty(true);
				} else if ($inputType == 'date') {
					$element->setImage($this->getSkinUrl('images/grid-cal.gif'));
					$element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
				} else if ($inputType == 'multiline') {
					$element->setLineCount($attribute->getMultilineCount());
				}
				if($element->getExtType() == 'multifile') {
					try {
						$fieldRenderer = Mage::getBlockSingleton('csvattribute/adminhtml_vendor_attribute_renderer_multifile');
						$fieldRenderer->setForm($form);
						$fieldRenderer->setConfigData($model[$attribute->getAttributeCode()]);
						$element->setRenderer($fieldRenderer);
					} catch(Exception $e){}
				}
				if($element->getExtType() == 'multiimage') {
					try {
						$fieldRenderer = Mage::getBlockSingleton('csvattribute/adminhtml_vendor_attribute_renderer_multiimage');
						$fieldRenderer->setForm($form);
						$fieldRenderer->setConfigData($model[$attribute->getAttributeCode()]);
						$element->setRenderer($fieldRenderer);
					} catch(Exception $e){}
				}
			}
		}
		if( $group->getAttributeGroupName() == 'General Information'){
			$element->setAfterElementHtml('<script type="text/javascript"> window.onload = function() { ced_csmarketplace = new ced_csmarketplace("'.$this->getUrl("*/*/checkAvailability",array("_secure"=>true,'id'=>$vendor_id)).'","edit_form"); } </script>');
		}
		
		return parent::_prepareForm();
	}

}


   