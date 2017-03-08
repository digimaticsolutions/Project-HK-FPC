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
class Ced_CsCmsPage_Block_Adminhtml_Cmspage_Edit_Tab_Meta
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * 
	 * @return void
	 */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * preparing form here
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     */
    protected function _prepareForm()
    {
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

        //$model = Mage::registry('cms_page');
		$model = Mage::getModel('cscmspage/cmspage')->load($this->getRequest()->getParam('page_id'));
		
        $fieldset = $form->addFieldset('meta_fieldset', array('legend' => Mage::helper('cms')->__('Meta Data'), 'class' => 'fieldset-wide'));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('cscmspage')->__('Keywords'),
            'title' => Mage::helper('cscmspage')->__('Meta Keywords'),
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('meta_description', 'textarea', array(
            'name' => 'meta_description',
            'label' => Mage::helper('cscmspage')->__('Description'),
            'title' => Mage::helper('cscmspage')->__('Meta Description'),
            'disabled'  => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_meta_prepare_form', array('form' => $form));

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
        return Mage::helper('cscmspage')->__('Meta Data');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cscmspage')->__('Meta Data');
    }

    /**
     * Returns status flag about this tab can be showen or not
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
