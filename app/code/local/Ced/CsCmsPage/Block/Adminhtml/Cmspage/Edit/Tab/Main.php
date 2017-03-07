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

class Ced_CsCmsPage_Block_Adminhtml_Cmspage_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * 
	 * Preparing form here
	 * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
	 */
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        //$model = Mage::register('cms_page');
       
		$model = Mage::getModel('cscmspage/cmspage')->load($this->getRequest()->getParam('page_id'));
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('Page Information')));

        if ($model->getPageId()) {
            $fieldset->addField('page_id', 'hidden', array(
                'name' => 'page_id',
            ));
            $VendorId = $model->getData('vendor_id');
    		$Vendor = Mage::getModel('csmarketplace/vendor')->load($VendorId);
    		$vendorShopUrl = 'vendorshop/'.$Vendor->getShopUrl().'/';
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('cscmspage')->__('Page Title'),
            'title'     => Mage::helper('cscmspage')->__('Page Title'),
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));
		//$shopurl = Mage::helper('cscmspage')->getVendorShopUrl();
        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
        	'label'     => Mage::helper('cscmspage')->__('URL Key'),
            'title'     => Mage::helper('cscmspage')->__('URL Key'),
            'required'  => true,
            'class'     => 'validate-identifier',
            'note'      => Mage::helper('cscmspage')->__('cms url prefix append by vendor shop url key -"'.$vendorShopUrl.'"'),
            'disabled'  => $isElementDisabled,
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
        } */

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('cscmspage')->__('Status'),
            'title'     => Mage::helper('cscmspage')->__('Page Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => $model->getAvailableStatuses(),
            'disabled'  => $isElementDisabled,
        ));
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
    	if($this->getRequest()->getParam('page_id')){
    		$identifier = $model->getData('identifier');
    		$identifier = str_replace($vendorShopUrl,'',$identifier);
    		$model->setData('identifier', $identifier);
		}
        
        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_main_prepare_form', array('form' => $form));
		
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cscmspage')->__('Page Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cscmspage')->__('Page Information');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
	
}
