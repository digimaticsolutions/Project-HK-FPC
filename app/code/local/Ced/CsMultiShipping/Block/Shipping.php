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
class Ced_CsMultiShipping_Block_Shipping extends Mage_Tax_Block_Checkout_Shipping
{
	/**
	 * Set Quote
	 *
	 * @param $quote
	 * @return void
	 */
    public function setQuote($quote){
        $this->_quote = $quote;
    }
    protected $_template = 'csmultishipping/shipping.phtml';
}
