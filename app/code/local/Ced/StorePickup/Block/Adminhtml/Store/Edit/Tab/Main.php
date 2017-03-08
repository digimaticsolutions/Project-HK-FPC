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
 * @package     Ced_StorePickup
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_StorePickup_Block_Adminhtml_Store_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
       $form = new Varien_Data_Form();
       $this->setForm($form);
       $fieldset = $form->addFieldset('test_form',
                                       array('legend'=>'Store information'));
        $fieldset->addField('store_name', 'text',
                       array(
                          'label' => 'Store Name',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'store_name',
                    ));
        $fieldset->addField('store_manager_name', 'text',
                         array(
                          'label' => 'Store Manager Name',
                          'class' => 'required-entry',
                          'required' => true,
                          'name' => 'store_manager_name',
                      ));
        $fieldset->addField('store_manager_email', 'text',
                    array(
                        'label' => 'Store Manager Email',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'store_manager_email',
                 ));
        
        $fieldset->addField('store_address', 'text',
        		array(
        				'label' => 'Store Address',
        				'class' => 'required-entry',
        				'required' => true,
        				'name' => 'store_address',
        		));
		
		
		$country = $fieldset->addField('store_country', 'select', array(
				'name'  => 'store_country',
				'label'     => 'Country',
				'values'    => Mage::getModel('adminhtml/system_config_source_country') ->toOptionArray(),
				//'onchange' => 'getstate(this)',
		));
		$fieldset->addField('store_city', 'text',
				array(
						'label' => 'Store City',
						'class' => 'required-entry',
						'required' => true,
						'name' => 'store_city',
				));
	/* 	$fieldset->addField('state', 'select', array(
				'name'  => 'state',
				'label'     => 'State',
				'values'    => Mage::getModel('modulename/modulename')
				->getstate('AU'),
		));
		
		$country->setAfterElementHtml("<script type=\"text/javascript\">
            function getstate(selectElement){
                var reloadurl = '". $this
				->getUrl('modulename/adminhtml_modulename/state') . "country/' + selectElement.value;
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (stateform) {
                        $('state').update('Searching...');
                    },
                    onComplete: function(stateform) {
                        $('state').update(stateform.responseText);
                    }
                });
            }
        </script>"); */
		
		
		$fieldset->addField('store_state', 'text',
				array(
						'label' => 'Store State',
						'class' => 'required-entry',
						'required' => true,
						'name' => 'store_state',
				));
		$fieldset->addField('store_zcode', 'text',
				array(
						'label' => 'Postal Code',
						'class' => 'required-entry',
						'required' => true,
						'name' => 'store_zcode',
				));
		$fieldset->addField('store_phone', 'text',
				array(
						'label' => 'Contact Number',
						'class' => 'required-entry',
						'required' => true,
						'name' => 'store_phone',
				));
		$fieldset->addField('shipping_price', 'text',
				array(
						'label' => 'Shipping Price',
						'class' => 'required-entry',
						'required' => true,
						'name' => 'shipping_price',
				));
		
     /*    $fieldset->addField('country', 'select',
          		array(
          				'label' => 'Country',
          				'class' => 'required-entry',
          				'required' => true,
          				'options' =>'',
          				'name' => 'country',
          		)); */
        $fieldset->addField('is_active', 'select',
          		array(
          				'label' => 'is_active',
          				'class' => 'required-entry',
          				'required' => true,
          				'options' => ['1' => __('Yes'), '0' => __('No')],
          				'name' => 'is_active',
          		));
          
          
          
          
          
          
 if ( Mage::registry('storepickup_data') )
 {
    $form->setValues(Mage::registry('storepickup_data')->getData());
  }
  return parent::_prepareForm();
 }
}