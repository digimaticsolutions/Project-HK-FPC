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
  * @package     Ced_Productfaq
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Productfaq_Block_Adminhtml_Faq_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
     * Setting Tab tile,id
     *
     */
	public function __construct()
	{
		parent::__construct();
		$this->setId('form_tabs');
		$this->setDestElementId('edit_form'); // this should be same as the form id define above
		$this->setTitle(Mage::helper('productfaq')->__('Faqs'));
	}
	/**
     *Adding Tab
     *
     */
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
				'label'     => Mage::helper('productfaq')->__('General'),
				'title'     => Mage::helper('productfaq')->__('General'),
				'content'   => $this->getLayout()->createBlock('productfaq/adminhtml_faq_edit_tab_form')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
