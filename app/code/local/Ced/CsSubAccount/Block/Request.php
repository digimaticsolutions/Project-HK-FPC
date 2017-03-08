
<!--
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
  * @package     Ced_CsSubAccount
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
-->

<?php 

class Ced_CsSubAccount_Block_Request extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	
	protected function _prepareLayout() {
		
		
		$this->setChild('back_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label'     => Mage::helper('cssubaccount')->__('Back'),
						'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/list', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
						'class' => 'btn btn-info uptransform'
				))
		);
		
		$this->setChild('send_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label'     => Mage::helper('cssubaccount')->__('Send'),
						//'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/send', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
						'class' => 'send scalable btn btn-success uptransform'
				))
		);
		
		Varien_Data_Form::setElementRenderer(
		$this->getLayout()->createBlock('csmarketplace/widget_form_renderer_element')
		);
		Varien_Data_Form::setFieldsetRenderer(
		$this->getLayout()->createBlock('csmarketplace/widget_form_renderer_fieldset')
		);
		Varien_Data_Form::setFieldsetElementRenderer(
		$this->getLayout()->createBlock('csmarketplace/widget_form_renderer_fieldset_element')
		);
	
		return parent::_prepareLayout();
	}
	
	public function __construct()
	{
		
		$this->_objectId = 'id';
		$this->_controller = 'customer';
		parent::__construct();
		$this->setTemplate('cssubaccount/view.phtml');
		$this->setId('customer_view');
	}
	
	public function getHeader()
	{
		return	$this->helper('cssubaccount')->__('Send Sub - Vendor Request To Customers');
	}
	
}