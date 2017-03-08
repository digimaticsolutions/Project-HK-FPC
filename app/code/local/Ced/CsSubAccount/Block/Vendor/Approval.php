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
 * Dashboard CsMarketplace Approval
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsSubAccount_Block_Vendor_Approval extends Ced_CsMarketplace_Block_Vendor_Abstract
{

	/**
	 * Set the Vendor object and Vendor Id in customer session
	 */
    public function __construct() {
		parent::__construct();
		if ($this->_getSession()->isLoggedIn()) {
			$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId($this->getCustomerId());
			if($vendor && $vendor->getId()) {
				$this->_getSession()->setData('vendor_id',$vendor->getId());
				$this->_getSession()->setData('vendor',$vendor);
			}
		}
	}
	
	/**
     * Get customer ID
     *
     * @return int
     */
	public function getCustomerId() {
		return $this->_getSession()->getCustomerId();
	}
	
	/**
     * Get customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer() {
        return $this->_getSession()->getCustomer();
    }
	
    public function getSubVendor() {
    	$collection = Mage::getModel('cssubaccount/cssubaccount')->load($this->_getSession()->getCustomerEmail(),'email');
    	//print_r($collection->getData());die;
    	return $collection;
    }
    
	/**
     * Approval message
     *
     * @return String
     */
	public function getApprovalMessage() {
		
		$message = '';
		
		if ($this->getSubVendor()->getStatus()==0) {
			$message .= Mage::helper('cssubaccount')->__('Your sub vendor account is under approval.');
		}
		else {
			$message .= Mage::helper('cssubaccount')->__('Your sub vendor account request is rejected.');
		}
		return $message;
	}
}
