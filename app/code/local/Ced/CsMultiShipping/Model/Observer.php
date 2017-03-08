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
 * @package     Ced_CsMultiShipping
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Observer model
 *
 * @category Ced
 * @package Ced_CsMultiShipping
 * @author CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMultiShipping_Model_Observer {
	
	/**
	 * Save shipping details into vorder table
	 */
	public function vorderSaveBefore($observer) {
		if(Mage::helper('csmultishipping')->isEnabled()){
			$vorder = $observer->getEvent()->getvorder();
			if (!$vorder->getId()) {
				$order = $vorder->getOrder();
				$quoteId = $order->getQuoteId();
				if ($quoteId) {
					$quote = Mage::getModel('sales/quote')->load ($quoteId);
					if ($quote && $quote->getId()) {
						$addresses = $quote->getAllShippingAddresses();
						//$flag = true;
						foreach ($addresses as $address) {
							if ($address) {
								$shippingMethod = $address->getShippingMethod();
								if (substr ($shippingMethod, 0, 12 ) == 'vendor_rates') {
									$shippingMethod = str_replace('vendor_rates_','',$shippingMethod);
								}
								$shippingMethods = explode ( Ced_CsMultiShipping_Model_Shipping::METHOD_SEPARATOR, $shippingMethod );
								$vendorId = 0;
								foreach ($shippingMethods as $method) {
									$rate = $address->getShippingRateByCode ($method);
									$methodInfo = explode(Ced_CsMultiShipping_Model_Shipping::SEPARATOR, $method);
									if (sizeof($methodInfo)!= 2) {
										continue;
									}
									$vendorId = isset($methodInfo [1])?$methodInfo[1]:"admin";
									
									if ($vendorId == $vorder->getVendorId()) {
										//$flag = false;
										$vorder->setShippingAmount(Mage::helper('directory')->currencyConvert($rate->getPrice(), $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode()));
										$vorder->setBaseShippingAmount($rate->getPrice());
										$vorder->setCarrier($rate->getCarrier());
										$vorder->setCarrierTitle($rate->getCarrierTitle());
										$vorder->setMethod($rate->getMethod());
										$vorder->setMethodTitle($rate->getMethodTitle());
										$vorder->setCode($method);
										$vorder->setShippingDescription($rate->getCarrierTitle ()."-".$rate->getMethodTitle ());
										break;
									}
								}
								/* if ($flag == true) {
									$vorder->setShippingAmount(null);
									$vorder->setBaseShippingAmount(null);
									$vorder->setCarrier(null);
									$vorder->setCarrierTitle(null);
									$vorder->setMethod(null);
									$vorder->setMethodTitle(null);
									$vorder->setCode(null);
									$vorder->setShippingDescription(null);
								} */
							}
						}
					}
				}
			}
		}
	}
}