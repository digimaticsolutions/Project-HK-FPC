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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Vendor payment method abstract model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract extends Mage_Core_Model_Abstract
{
	const SHIPPING_SECTION = 'shipping';
    protected $_code = '';
	protected $_fields = array();
	protected $_codeSeparator = '-';
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStore() {
		$storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        if($storeId)
			return Mage::app()->getStore($storeId);
		else 
			return Mage::app()->getStore();
	 }
	 
	 /**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStoreId() {
		return $this->getStore()->getId();
	 }
	
	
	/**
	 * Get the code
	 *
	 * @return string
	 */
	public function getCode() {
		return $this->_code;
	}
	
	/**
	 * Get the code separator
	 *
	 * @return string
	 */
	public function getCodeSeparator() {
		return $this->_codeSeparator;
	}
	
	/**
	 *  Retreive input fields
	 *
	 * @return array
	 */
	public function getFields() {
		$this->_fields = array();
		$this->_fields['active'] = array('type'=>'select','values'=>array(array('label'=>Mage::helper('csmultishipping')->__('Yes'),'value'=>1),array('label'=>Mage::helper('csmultishipping')->__('No'),'value'=>0)));
		return $this->_fields;
	}
	
	/**
	 * Retreive labels
	 *
	 * @param string $key
	 * @return string
	 */
	public function getLabel($key) {
		switch($key) {
			case 'active' : return Mage::helper('csmultishipping')->__('Active'); break;
			default : return Mage::helper('csmultishipping')->__($key); break;
		}
	}
	
	public function validateSpecificMethod($methodData){
		if(count($methodData)>0){
			return true;
		}
		else
			return false;
	}
}
