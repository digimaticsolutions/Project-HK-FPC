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
class Ced_CsMultiShipping_Model_Vsettings_Shipping_Address extends Mage_Core_Model_Abstract
{
    protected $_code = 'address';
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
		$this->_fields['country_id'] = array('type'=>'select','required'=>true,'values'=>Mage::getModel('adminhtml/system_config_source_country')->toOptionArray());
		$this->_fields['region_id'] = array('type'=>'select','required'=>true,'values'=>array(array('label'=>Mage::helper('csmultishipping')->__('Please select region, state or province'),'value'=>'')));
		$this->_fields['region'] = array('type'=>'text','required'=>true);
		$this->_fields['city'] = array('type'=>'text','required'=>true);
		$this->_fields['postcode'] = array('type'=>'text','required'=>true);
		$this->_fields['postcode']['after_element_html']="<script>new RegionUpdater('address-country_id', 'address-region', 'address-region_id',".Mage::helper('directory')->getRegionJson().", undefined, 'address-postcode');</script>";
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
			case 'label'  :  return Mage::helper('csmultishipping')->__('Origin Address Details'); break;
			case 'country_id' : return Mage::helper('csmultishipping')->__('Country'); break;
			case 'region_id' : return Mage::helper('csmultishipping')->__('State/Province'); break;
			case 'region' : return ""; break;
			case 'city' : return Mage::helper('csmultishipping')->__('City'); break;
			case 'postcode' : return Mage::helper('csmultishipping')->__('Zip/Postal Code'); break;
			default : return Mage::helper('csmultishipping')->__($key); break;
		}
	}
}
