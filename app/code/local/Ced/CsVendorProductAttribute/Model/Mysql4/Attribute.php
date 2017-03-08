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
  * @package     Ced_CsVendorProductAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * CsVendorProductAttribute entity resource model
 *
 * @category    Ced
 * @package     Ced_CsVendorProductAttribute
 */
class Ced_CsVendorProductAttribute_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
	/**
     * Resource initialization
     */

	public function _construct()
	{
		$this->_init('csvendorproductattribute/attribute', 'id');
     
	}
}
