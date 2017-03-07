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

class Ced_CsCmsPage_Model_Vendorblock extends Mage_Core_Model_Abstract
{
	const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    /**
     * 
     * @see Varien_Object::_construct()
     */
	protected function _construct()
	{
		$this->_init('cscmspage/vendorblock');
	}
	/**
	 * Innitialize Available Status
	 */
	public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('cscmspage')->__('Approved'),
            self::STATUS_DISABLED => Mage::helper('cscmspage')->__('Pending'),
        ));

        Mage::dispatchEvent('cms_page_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }
    
    /**
     * Innitialize Vendor Block Status
     */
    public function getVendorCmsStatus(){
    	$statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('cscmspage')->__('Enable'),
            self::STATUS_DISABLED => Mage::helper('cscmspage')->__('Disable'),
        ));

        Mage::dispatchEvent('cms_page_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }
}