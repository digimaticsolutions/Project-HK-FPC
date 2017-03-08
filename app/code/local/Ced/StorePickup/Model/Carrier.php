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
 * @package     Ced_StorePickup
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_StorePickup_Model_Carrier
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
	protected $_code = 'ced_storepickup';
	 public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  	{ 
		die("lkjh");
		$result = Mage::getModel('shipping/rate_result');
		$result->append($this->_getDefaultRate());
	
		return $result;
	}
	
	public function getAllowedMethods()
	{
		 return array(
				'ced_storepickup' => 'ggghgghjgj',
		); 
	}
}