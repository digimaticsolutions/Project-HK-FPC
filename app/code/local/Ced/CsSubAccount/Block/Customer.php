<?php 


class Ced_CsSubAccount_Block_Customer extends Mage_Adminhtml_Block_Widget_Container
{
	public function __construct()
	{ //die(get_class($this));
		parent::__construct();
		$this->setTemplate('cssubaccount/customer.phtml');
		$this->setId('customer_edit');
	}
	
	protected function _prepareLayout()
	{
		$newurl=Mage::getUrl('*/*/request');
		$this->_addButton('add_new', array(
				'label'   => Mage::helper('cssubaccount')->__('Request Customer'),
				'onclick' => "setLocation('{$newurl}')",
				'class'   => 'btn btn-primary uptransform'
						));
		
		$this->setChild('grid', $this->getLayout()->createBlock('cssubaccount/grid', 'customer.grid'));
		return parent::_prepareLayout();
	}
	
	public function getAddNewButtonHtml()
	{
		return $this->getChildHtml('add_new_button');
	}
	
	/**
	 * Render grid
	 *
	 * @return string
	 */
	public function getGridHtml()
	{
		return $this->getChildHtml('grid');
	}
	
	/**
	 * Check whether it is single store mode
	 *
	 * @return bool
	 */
	public function isSingleStoreMode()
	{
		if (!Mage::app()->isSingleStoreMode()) {
			return false;
		}
		return true;
	}
}