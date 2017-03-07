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
  * @author  	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsCommission_Block_Adminhtml_Vendor_Entity_Edit_Tab_Configurations extends Mage_Adminhtml_Block_System_Config_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * Set Tab Label
	 */
	public function getTabLabel() {
        return $this->__('Commission Configuration');
    }

    /**
     * Set Tab Title
     */
    public function getTabTitle() {
        return $this->__('Commission Configuration');
    }

    /**
     * @return boolean
     */
    public function canShowTab() {
		if (Mage::registry('vendor_data') && is_object(Mage::registry('vendor_data')) && Mage::registry('vendor_data')->getId()) {
			return true;
		}
        return false;
    }
	
    /**
     * Hide tab
     */
    public function isHidden() {
		return !$this->canShowButton();
    }
	
    /**
     * Add Tab After
     */
	public function getAfter() {
		return 'vpayments'; 
	}
	
	/**
	 * Show Button
	 * @return boolean
	 */
	public function canShowButton() {
		if (Mage::registry('vendor_data') && is_object(Mage::registry('vendor_data')) && Mage::registry('vendor_data')->getId()) {
			return true;
		}
        return false;	
	}
	
	/**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    protected function _initObjects()
    {
		parent::_initObjects();
        //$this->_defaultFieldsetRenderer = Mage::getBlockSingleton('csgroup/adminhtml_system_config_form_fieldset');
        //$this->_defaultFieldRenderer = Mage::getBlockSingleton('csgroup/adminhtml_system_config_form_field');
        return $this;
    }
    /**
     *
     * @return form
     */
	protected function _prepareForm(){ 	
		try {
			$this->initForm();
		} catch (Exception $e) {
			print_r($e->getMessage());die;
		}
	}
	
	 /**
     * Init fieldset fields
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param Varien_Simplexml_Element $group
     * @param Varien_Simplexml_Element $section
     * @param string $fieldPrefix
     * @param string $labelPrefix
     * @return Mage_Adminhtml_Block_System_Config_Form
     */
    public function initFields($fieldset, $group, $section, $fieldPrefix='', $labelPrefix='')
    {
        if (!$this->_configDataObject) {
            $this->_initObjects();
        }

        // Extends for config data
        $configDataAdditionalGroups = array();

        foreach ($group->fields as $elements) {

            $elements = (array)$elements;
            // sort either by sort_order or by child node values bypassing the sort_order
            if ($group->sort_fields && $group->sort_fields->by) {
                $fieldset->setSortElementsByAttribute((string)$group->sort_fields->by,
                    ($group->sort_fields->direction_desc ? SORT_DESC : SORT_ASC)
                );
            } else {
                usort($elements, array($this, '_sortForm'));
            }

            foreach ($elements as $e) {
                if (!$this->_canShowField($e)) {
                    continue;
                }

                /**
                 * Look for custom defined field path
                 */
                $path = (string)$e->config_path;
                if (empty($path)) {
                    $path = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $e->getName();
                } elseif (strrpos($path, '/') > 0) {
                    // Extend config data with new section group
                    $groupPath = substr($path, 0, strrpos($path, '/'));
                    if (!isset($configDataAdditionalGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject->extendConfig($groupPath, false, $this->_configData);
                        $configDataAdditionalGroups[$groupPath] = true;
                    }
                }
				
                $id = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $e->getName();
				$origPath = $path;
				if (Mage::registry('vendor_data') && is_object(Mage::registry('vendor_data')) && Mage::registry('vendor_data')->getId()) {
					$path =  Mage::registry('vendor_data')->getId(). '/' . $path;
					
					/*$id = Mage::registry('vendor_data')->getGroupCode().'_'.$id;*/
				}
	
                 if (isset($this->_configData[$path])) {
                    $data = $this->_configData[$path];
                    $inherit = false;
					$useDefault = true;
                } else {
					$data = $this->_configRoot->descend($path);
                    $inherit = false;
					$useDefault = true;
				}
				
				if(!strlen($data)) {
					if(isset($this->_configData[$origPath])) {
						$data = $this->_configData[$origPath];
						$inherit = true;
						$useDefault = true;
					} else {
						$data = $this->_configRoot->descend($origPath);
						$inherit = true;
						$useDefault = true;
					}
				}

                if ($e->frontend_model) {
                    $fieldRenderer = Mage::getBlockSingleton((string)$e->frontend_model);
                } else {
                    $fieldRenderer = $this->_defaultFieldRenderer;
                }

                $fieldRenderer->setForm($this);
                $fieldRenderer->setConfigData($this->_configData);

                $helperName = $this->_configFields->getAttributeModule($section, $group, $e);
                $fieldType  = (string)$e->frontend_type ? (string)$e->frontend_type : 'text';
                $name       = 'groups['.$group->getName().'][fields]['.$fieldPrefix.$e->getName().'][value]';
                $label      =  Mage::helper($helperName)->__($labelPrefix).' '.Mage::helper($helperName)->__((string)$e->label);
                $hint       = (string)$e->hint ? Mage::helper($helperName)->__((string)$e->hint) : '';

                if ($e->backend_model) {
                    $model = Mage::getModel((string)$e->backend_model);
                    if (!$model instanceof Mage_Core_Model_Config_Data) {
                        Mage::throwException('Invalid config field backend model: '.(string)$e->backend_model);
                    }
					
					
                    $model->setPath($path)
                        ->setValue($data)
                        ->setWebsite($this->getWebsiteCode())
                        ->setStore($this->getStoreCode())
                        ->afterLoad();
                    $data = $model->getValue();
                }

                $comment    = $this->_prepareFieldComment($e, $helperName, $data);
                $tooltip    = $this->_prepareFieldTooltip($e, $helperName);

                if ($e->depends) {
                    foreach ($e->depends->children() as $dependent) {
                        $dependentId = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $dependent->getName();
                        $shouldBeAddedDependence = true;
                        $dependentValue          = (string) $dependent;
                        $dependentFieldName      = $fieldPrefix . $dependent->getName();
                        $dependentField          = $group->fields->$dependentFieldName;
                        /*
                         * If dependent field can't be shown in current scope and real dependent config value
                         * is not equal to preferred one, then hide dependence fields by adding dependence
                         * based on not shown field (not rendered field)
                         */
                        if (!$this->_canShowField($dependentField)) {
                            $dependentFullPath = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $dependent->getName();
                            $shouldBeAddedDependence = $dependentValue != Mage::getStoreConfig($dependentFullPath, $this->getStoreCode());
                        }
                        if($shouldBeAddedDependence) {
                            $this->_getDependence()
                                ->addFieldMap($id, $id)
                                ->addFieldMap($dependentId, $dependentId)
                                ->addFieldDependence($id, $dependentId, $dependentValue);
                        }
                    }
                }
				/* Set The field id and field name as per group code */
				$field = $fieldset->addField($id, $fieldType, array(
                    'name'                  => $name,
                    'label'                 => $label,
                    'comment'               => $comment,
                    'tooltip'               => $tooltip,
                    'hint'                  => $hint,
                    'value'                 => $data,
                    'inherit'               => $inherit,
                    'class'                 => $e->frontend_class,
                    'field_config'          => $e,
                    'scope'                 => $this->getScope(),
                    'scope_id'              => $this->getScopeId(),
                    'scope_label'           => $this->getScopeLabel($e),
                    'can_use_default_value' => $useDefault, /* $this->canUseDefaultValue((int)$e->show_in_default), */
                    'can_use_website_value' => false,/* $this->canUseWebsiteValue((int)$e->show_in_website), */
                ));
                $this->_prepareFieldOriginalData($field, $e);

                if (isset($e->validate)) {
                    $field->addClass($e->validate);
                }

                if (isset($e->frontend_type) && 'multiselect' === (string)$e->frontend_type && isset($e->can_be_empty)) {
                    $field->setCanBeEmpty(true);
                }

                $field->setRenderer($fieldRenderer);

                if ($e->source_model) {
                    // determine callback for the source model
                    $factoryName = (string)$e->source_model;
                    $method = false;
                    if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
                        array_shift($matches);
                        list($factoryName, $method) = array_values($matches);
                    }

                    $sourceModel = Mage::getSingleton($factoryName);
                    if ($sourceModel instanceof Varien_Object) {
                        $sourceModel->setPath($path);
                    }
                    if ($method) {
                        if ($fieldType == 'multiselect') {
                            $optionArray = $sourceModel->$method();
                        } else {
                            $optionArray = array();
                            foreach ($sourceModel->$method() as $value => $label) {
                                $optionArray[] = array('label' => $label, 'value' => $value);
                            }
                        }
                    } else {
                        $optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
                    }
                    $field->setValues($optionArray);
                }
            }
        }
        return $this;
    }
	
	/**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getSectionCode()
    {
        return 'ced_csmarketplace';
    }
	
	/**
     * Checking field visibility
     *
     * @param   Varien_Simplexml_Element $field
     * @return  bool
     */
    protected function _canShowField($field)
    {
		$canShow = parent::_canShowField($field);
        if($canShow) {
			return (int)$field->show_in_commission;
		}
		return $canShow;
		
    }
	
    /**
     * @return  html
     */
	protected function _toHtml() {
		if($this->getRequest()->isAjax()) {
			return parent::_toHtml();
		}
		$switcher = '';
		if(Mage::helper('core')->isModuleEnabled('Ced_CsGroup')) {
			$switcher = $this->getLayout()->createBlock('adminhtml/template')->setStoreSelectOptions($this->getStoreSelectOptions())->setTemplate('csgroup/system/config/switcher.phtml')->toHtml();
			$switcher .= '<style>.switcher p{ display: none; }</style>';
		}
		$parent = '<div id="vendor_group_configurations_section">'.parent::_toHtml().'</div>';
		if(strlen($parent) <= 50) {
			$parent .= '<div id="messages"><ul class="messages"><li class="error-msg"><ul><li><span>'.Mage::helper('csgroup')->__('No Configurations are Available for Current Configuration Scope. Please Up the Configuration Scope by One Level.').'</span></li></ul></li></ul></div>';
			return $parent/* .$switcher */;
		}
		return /* $switcher. */$parent;
	}
	
	/**
     * Enter description here...
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        $url = Mage::getModel('adminhtml/url');

        $options = array();
        $options['default'] = array(
            'label'    => Mage::helper('adminhtml')->__('Default Config'),
            'url'      => $url->getUrl('adminhtml/adminhtml_group/configuration', array('section'=>$section,'gcode' => $this->getRequest()->getParam('gcode',false),'module_block_handle' => 'cscommission', 'block_name'=>'adminhtml_vendor_entity_edit_tab_configurations')),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#ccc; font-weight:bold;',
        );

        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $websiteShow = true;
                        $options['website_' . $website->getCode()] = array(
                            'label'    => $website->getName(),
                            'url'      => $url->getUrl('adminhtml/adminhtml_group/configuration', array('section'=>$section, 'website'=>$website->getCode(), 'gcode' => $this->getRequest()->getParam('gcode',false), 'module_block_handle' => 'cscommission', 'block_name'=>'adminhtml_vendor_entity_edit_tab_configurations')),
                            'selected' => !$curStore && $curWebsite == $website->getCode(),
                            'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
                        );
                    }
                    // if (!$groupShow) {
                        // $groupShow = true;
                        // $options['group_' . $group->getId() . '_open'] = array(
                            // 'is_group'  => true,
                            // 'is_close'  => false,
                            // 'label'     => $group->getName(),
                            // 'style'     => 'padding-left:32px;'
                        // );
                    // }
                    // $options['store_' . $store->getCode()] = array(
                        // 'label'    => $store->getName(),
                        // 'url'      => $url->getUrl('*/*/configuration', array('section'=>$section, 'website'=>$website->getCode(), 'store'=>$store->getCode())),
                        // 'selected' => $curStore == $store->getCode(),
                        // 'style'    => '',
                    // );
                }
                // if ($groupShow) {
                    // $options['group_' . $group->getId() . '_close'] = array(
                        // 'is_group'  => true,
                        // 'is_close'  => true,
                    // );
                // }
            }
        }

        return $options;
    }
}