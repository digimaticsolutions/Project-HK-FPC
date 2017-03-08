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
  * @package     Ced_CsCommission
  * @author  	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */


/**
 * Core helper data
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author 		CedCommerce Core Team <connect@cedcommerce.com >
 */
 
class Ced_CsCommission_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 *  @param Int $price, Int $rate, string $method
	 *  @return Commission Fee
	 */
	public function calculateFee($price,$rate = 0,$method = 'percentage') {
		switch($method) {
			case Ced_CsCommission_Block_Adminhtml_Vendor_Rate_Method::METHOD_FIXED :
				return min($price,$rate);
				break;
			case Ced_CsCommission_Block_Adminhtml_Vendor_Rate_Method::METHOD_PERCENTAGE :
				return max((($rate * $price) / 100), 0) ;
				break;
		}
	}
}