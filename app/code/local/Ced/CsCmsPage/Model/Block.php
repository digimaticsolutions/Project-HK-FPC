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

class Ced_CsCmsPage_Model_Block extends Mage_Core_Model_Abstract
{
	const CACHE_TAG     = 'cms_block';
    protected $_cacheTag= 'cms_block';
    
    /**
     * 
     * Initilaize Resource Model
     * @see Varien_Object::_construct()
     */
	protected function _construct()
	{
		$this->_init('cscmspage/block');
	}
	

    /**
     * Prevent blocks recursion
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $needle = 'block_id="' . $this->getBlockId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::_beforeSave();
        }
        Mage::throwException(
            Mage::helper('cms')->__('The static block content cannot contain  directive with its self.')
        );
    }
}