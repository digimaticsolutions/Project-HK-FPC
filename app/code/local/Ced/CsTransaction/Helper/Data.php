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
 * @category    Ced;
 * @package     Ced_CsTransaction 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsTransaction_Helper_Data extends Mage_Core_Helper_Abstract
{
    
	/**
	 * Function for getting shipping amount for vendor Credit or Debit
	 *
	 * @return string
	 */
	public function getAvailableShipping($vorder,$type)
	{
		if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {	
					if($vorder->getShippingPaid()>$vorder->getShippingRefunded())
					{
						$shippingAmount=$vorder->getShippingPaid()-$vorder->getShippingRefunded();
					}
					elseif($vorder->getShippingPaid()==$vorder->getShippingRefunded()&&$vorder->getShippingPaid()!=0)
					{
						$shippingAmount='Refunded';
					}
					else
					{
						$shippingAmount='N/A';
					}
				}
				else
				{
					if($vorder->getShippingAmount()>$vorder->getShippingPaid())
					{
						$shippingAmount=$vorder->getShippingAmount()-$vorder->getShippingPaid();
					}
					elseif($vorder->getShippingAmount()==$vorder->getShippingPaid()&&$vorder->getShippingAmount()!=0)
					{
						$shippingAmount='Paid';
					}
					else
					{
						$shippingAmount='N/A';
					}
				}
				return $shippingAmount;
	}
}
