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
  * @package     Ced_CsCmsPage
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsCmsPage_Block_Adminhtml_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_form');
        $this->setTitle(Mage::helper('cscmspage')->__('Block Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * 
     * Prepare Form here
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     */
    protected function _prepareForm()
    {
    	$isElementDisabled = false;
    	if($this->getRequest()->getParam('block_id')){
			$model = Mage::getModel('cscmspage/block')->load($this->getRequest()->getParam('block_id'));
       }else{
       		$model = Mage::getModel('cscmspage/block');
       }	
		$VendorId = $model->getData('vendor_id');
		$Vendor = Mage::getModel('csmarketplace/vendor')->load($VendorId);
    	$vendorShopUrl = $Vendor->getShopUrl();
        

        $form = new Varien_Data_Form(
            array('id' => 'edit_form',
            'action' => $this->getUrl('adminhtml/adminhtml_vendorblock/save', array('block_id' => $this->getRequest()->getParam('block_id'))), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cscmspage')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getBlockId()) {
            $fieldset->addField('block_id', 'hidden', array(
                'name' => 'block_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('cscmspage')->__('Block Title'),
            'title'     => Mage::helper('cscmspage')->__('Block Title'),
            'required'  => true,
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => Mage::helper('cscmspage')->__('Identifier'),
            'title'     => Mage::helper('cscmspage')->__('Identifier'),
            'required'  => true,
        	'note'      => Mage::helper('cscmspage')->__('Block Identifier prefix with vendor shop urlkey -"'.$vendorShopUrl.'"'),
            'class'     => 'validate-xml-identifier',
        ));

        /**
         * Check is single store mode
         */
        //if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cscmspage')->__('Store View'),
                'title'     => Mage::helper('cscmspage')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        //}
        /*else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }*/

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('cscmspage')->__('Status'),
            'title'     => Mage::helper('cscmspage')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('cscmspage')->__('Enabled'),
                '0' => Mage::helper('cscmspage')->__('Disabled'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }
		
        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('cscmspage')->__('Content'),
            'title'     => Mage::helper('cscmspage')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig()
        ));
    	if($this->getRequest()->getParam('block_id')){
    		$identifier = $model->getData('identifier');
    		$VendorId = $model->getData('vendor_id');
    		$vendorShopUrl = $vendorShopUrl.'_';
			$identifier = str_replace($vendorShopUrl,'',$identifier);    	
    		$model->setData('identifier', $identifier);
		}

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
