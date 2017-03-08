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
 * Product attributes tab
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Form
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
        
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')
        		&& Mage::getSingleton('cms/wysiwyg_config')->isEnabled()
        ) {
        	$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
	
		return $this ;
	}

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
    	if ($group = $this->getGroup()) {
    		$form = new Varien_Data_Form();
    		/**
    		 * Initialize product object as form property
    		 * for using it in elements generation
    		*/
    		$form->setDataObject(Mage::registry('product'));
    	
    		$fieldset = $form->addFieldset('group_fields'.$group->getId(),
    				array(
    						'legend'=>Mage::helper('catalog')->__($group->getAttributeGroupName()),
    						'class'=>'fieldset-wide',
    				));
    	
    		$attributes = $this->getGroupAttributes();
    	
    		$this->_setFieldset($attributes, $fieldset, array('gallery'));
    	
    		$urlKey = $form->getElement('url_key');
            if ($urlKey) {
    			$urlKey->setRenderer(
    					$this->getLayout()->createBlock('csproduct/edit_form_renderer_attribute_urlkey')
    			);
    		}
    	
    		$tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
    			$tierPrice->setRenderer(
    					$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
    					->setTemplate('csproduct/edit/form/renderer/price/tier.phtml')
    			);
    		}
    		
    		$groupPrice = $form->getElement('group_price');
    		if ($groupPrice) {
    			$groupPrice->setRenderer(
    					$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_group')
    					->setTemplate('csproduct/edit/form/renderer/price/group.phtml')
    			);
    		}
    		
    		
    		if ($this->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
	    		if ($special_price = $form->getElement('special_price')) {
	    			$special_price->setRenderer(
	    					$this->getLayout()->createBlock('csproduct/edit_form_renderer_bundle_attributes_special')
	    					->setDisableChild(false)
	    			);
	    		}
	    		
	    		if ($sku = $form->getElement('sku')) {
	    			$sku->setRenderer(
	    					$this->getLayout()->createBlock('csproduct/edit_form_renderer_bundle_attributes_extend')
	    					->setDisableChild(false)
	    			);
	    		}
	    		
	    		if ($price = $form->getElement('price')) {
	    			$price->setRenderer(
	    					$this->getLayout()->createBlock('csproduct/edit_form_renderer_bundle_attributes_extend')
	    					->setDisableChild(true)
	    			);
	    		}
	    		
	    		
	    		if ($tax = $form->getElement('tax_class_id')) {
	    			$tax->setAfterElementHtml(
	    					'<script type="text/javascript">'
	    					. "
	                function changeTaxClassId() {
	                    if ($('price_type').value == '" . Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . "') {
	                        $('tax_class_id').disabled = true;
	                        $('tax_class_id').value = '0';
	                        $('tax_class_id').removeClassName('required-entry');
	                        if ($('advice-required-entry-tax_class_id')) {
	                            $('advice-required-entry-tax_class_id').remove();
	                        }
	                    } else {
	                        $('tax_class_id').disabled = false;
	                        " . ($tax->getRequired() ? "$('tax_class_id').addClassName('required-entry');" : '') . "
	                    }
	                }
	    		
	                $('price_type').observe('change', changeTaxClassId);
	                changeTaxClassId();
	                "
	    					. '</script>'
	    			);
	    		}
	    		
	    		if ($weight = $form->getElement('weight')) {
	    			$weight->setRenderer(
	    					$this->getLayout()->createBlock('csproduct/edit_form_renderer_bundle_attributes_extend')
	    					->setDisableChild(true)
	    			);
	    		}
	    		
	    		if ($weight = $form->getElement('tier_price')) {
	    			$weight->setRenderer(
	    					$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
    						->setTemplate('csproduct/edit/form/renderer/price/tier.phtml')
	    					->setPriceColumnHeader(Mage::helper('bundle')->__('Percent Discount'))
	    					->setPriceValidation('validate-greater-than-zero validate-percents')
	    			);
	    		}
	    		$groupPrice = $form->getElement('group_price');
	    		if ($groupPrice) {
	    			$groupPrice->setRenderer(
	    					$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_group')
    					->setTemplate('csproduct/edit/form/renderer/price/group.phtml')
	    					->setPriceColumnHeader(Mage::helper('bundle')->__('Percent Discount'))
	    					->setPriceValidation('validate-greater-than-zero validate-percents')
	    			);
	    		}
	    		
	    		$mapEnabled = $form->getElement('msrp_enabled');
	    		if ($mapEnabled && $this->getCanEditPrice() !== false) {
	    			$mapEnabled->setAfterElementHtml(
	    					'<script type="text/javascript">'
	    					. "
                function changePriceTypeMap() {
                    if ($('price_type').value == " . Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . ") {
                        $('msrp_enabled').setValue("
	    					. Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_NO
	    					. ");
                        $('msrp_enabled').disable();
                        $('msrp_display_actual_price_type').setValue("
	    					. Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG
	    					. ");
                        $('msrp_display_actual_price_type').disable();
                        $('msrp').setValue('');
                        $('msrp').disable();
                    } else {
                        $('msrp_enabled').enable();
                        $('msrp_display_actual_price_type').enable();
                        $('msrp').enable();
                    }
                }
                document.observe('dom:loaded', function() {
                    $('price_type').observe('change', changePriceTypeMap);
                    changePriceTypeMap();
                });
                "
	    					. '</script>'
	    			);
	    		}
    		}
    	
    		if ($form->getElement('meta_description')) {
    			$form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
    		}
    		
    		if ($weight=$form->getElement('weight')) {
    			$weight->addClass('validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999');
    		}
    		
    		if ($weight=$form->getElement('description')) {
    			$weight->setAfterElementHtml('<script>document.getElementById("description").setAttribute("rows", 5);</script>');
    		}
    		
    		if ($weight=$form->getElement('short_description')) {
    			$weight->setAfterElementHtml('<script>document.getElementById("short_description").setAttribute("rows", 5);</script>');
    		}
    	
    		$values = Mage::registry('product')->getData();
    		/**
    		 * Set attribute default values for new product
    		*/
    		if (!Mage::registry('product')->getId()) {
    			foreach ($attributes as $attribute) {
    				if (!isset($values[$attribute->getAttributeCode()])) {
    					$values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
    				}
    			}
    		}
    	
    		if (Mage::registry('product')->hasLockedAttributes()) {
    			foreach (Mage::registry('product')->getLockedAttributes() as $attribute) {
    				if ($element = $form->getElement($attribute)) {
    					$element->setReadonly(true, true);
    				}
    			}
    		}
    	
    		Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form'=>$form));
    	
    		$form->addValues($values);
    		$form->setFieldNameSuffix('product');
    		$this->setForm($form);
    	}
    	
    }
    
    
    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude=array())
    {
    	$this->_addElementTypes($fieldset);
    	foreach ($attributes as $attribute) {
    		/* @var $attribute Mage_Eav_Model_Entity_Attribute */
    		if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
    			continue;
    		}
    		if ( ($inputType = $attribute->getFrontend()->getInputType())
    				&& !in_array($attribute->getAttributeCode(), $exclude)
    				&& ('media_image' != $inputType)
    		) {
    					
    			$fieldType      = $inputType;
    			if (!empty($rendererClass) && $attribute->getAttributeCode()!=="gift_message_available") {
    				$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
    				$fieldset->addType($fieldType, $rendererClass);
    			}
    			$attribute->setStoreId($this->getRequest()->getParam('store',0));
    			$element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
    					array(
    							'name'      => $attribute->getAttributeCode(),
    							'label'     => $attribute->getFrontend()->getLabel(),
    							'class'     => $attribute->getFrontend()->getClass(),
    							'required'  => $attribute->getIsRequired(),
    							'note'      => $attribute->getNote(),
    					)
    			)
    			->setEntityAttribute($attribute);
    
    			$element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
    
    			if ($inputType == 'select') {
    				if($attribute->getAttributeCode()=="msrp_enabled"){
    					$options=array();
    					$options=$attribute->getSource()->getAllOptions(true, true);
    					foreach ($options as $key => $value){
    						if($value['value']==Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_USE_CONFIG){
    							unset($options[$key]);
    							break;
    						}
    					}
    					$element->setValues($options);
    				}
    				else if($attribute->getAttributeCode()=="msrp_display_actual_price_type"){
    					$options=array();
    					$options=$attribute->getSource()->getAllOptions(true, true);
    					foreach ($options as $key => $value){
    						if($value['value']==Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG){
    							unset($options[$key]);
    							break;
    						}
    					}
    					$element->setValues($options);
    				}
    				else
    					$element->setValues($attribute->getSource()->getAllOptions(true, true));
    			} else if ($inputType == 'multiselect') {
    				$element->setValues($attribute->getSource()->getAllOptions(false, true));
    				$element->setCanBeEmpty(true);
    			} else if ($inputType == 'date') {
    				$element->setImage($this->getSkinUrl('images/calendar.gif'));
    				$element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
    			} else if ($inputType == 'datetime') {
    				$element->setImage($this->getSkinUrl('images/calendar.gif'));
    				$element->setTime(true);
    				$element->setStyle('width:50%;');
    				$element->setFormat(
    						Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
    				);
    			} else if ($inputType == 'multiline') {
    				$element->setLineCount($attribute->getMultilineCount());
    			}
    		}
    	}
    }
    

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
    			'price'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
    			'gallery'  => Mage::getConfig()->getBlockClassName('csproduct/edit_form_gallery'),
    			'image'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
    			'boolean'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean'),
        		'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg'),
        		'weight'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_weight')
        		
    	);
    
    	$response = new Varien_Object();
    	$response->setTypes(array());
    	Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response' => $response));
    	foreach ($response->getTypes() as $typeName => $typeClass) {
    		$result[$typeName] = $typeClass;
    	}
    
    	return $result;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {	
    	return '';
    }
    
    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
    	if (!($this->getData('product') instanceof Mage_Catalog_Model_Product)) {
    		$this->setData('product', Mage::registry('product'));
    	}
    	return $this->getData('product');
    }
    
   
}
