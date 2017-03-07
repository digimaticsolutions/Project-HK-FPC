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
 * @package     Ced_CsOrder 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsOrder_Block_Cart extends Mage_Checkout_Block_Cart
{
   /**
	 * Get Checkout Url
	 *
	 * @return string
	 */
    public function getCheckoutUrl()
    {
        /* return $this->getUrl('checkout/onepage', array('_secure'=>true)); */
		if(Mage::helper('csorder')->isActive())
		{
			return $this->getUrl('csorder/multishipping', array('_secure'=>true));
		}
		return parent::getCheckoutUrl();
    }

  
}
