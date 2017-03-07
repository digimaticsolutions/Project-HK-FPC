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
class Ced_CsMultiShipping_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping {
    protected $_vendor_rates;
    
    /**
     * Set parameters
     *
     * @return void
     */
    protected function _construct()
    {
    	parent::_construct();
    	if(Mage::helper('csmultishipping')->isEnabled())
    		$this->setTemplate('csmultishipping/cart/shipping.phtml');
    	else
    		$this->setTemplate('checkout/cart/shipping.phtml');
    }
      
    /**
     * Get Rates By Vendor
     *
     * @return int
     */
    public function getRatesByVendor(){
        if (empty($this->_vendor_rates)) {
            $groups = array();
            $rateCollection = $this->getAddress()->getShippingRatesCollection();
            foreach($rateCollection as $rate){
                if($rate->isDeleted()) 
                	continue;
                if($rate->getCarrier() == 'vendor_rates') 
                	continue;
				
                $tmp = explode(Ced_CsMultiShipping_Model_Shipping::SEPARATOR, $rate->getCode());
                $vendorId = isset($tmp[1])?$tmp[1]:"admin";
                $vendor = Mage::getModel('csmarketplace/vendor');
                if($vendorId && $vendorId!="admin"){
                	$vendor = $vendor->load($vendorId);                		
                }
                
                if(!isset($groups[$vendorId])) 
                	$groups[$vendorId] = array();
        
                $groups[$vendorId]['title'] = $vendor->getId()?$vendor->getPublicName():Mage::app()->getWebsite()->getName();
                if(!isset($groups[$vendorId]['rates'])) 
                	$groups[$vendorId]['rates'] = array();
                $groups[$vendorId]['rates'][] = $rate;
            }
            ksort($groups);
            $this->_vendor_rates = $groups;
          
        }
        return $this->_vendor_rates;
    }
    
    /**
     * Get Selected Vendor Shipping
     *
     * @return string
     */
    function getSelectedVendorShipping(){
    	$selectedMethod = str_replace("vendor_rates_", '', $this->getAddressShippingMethod());
    	$selectedMethods = explode(Ced_CsMultiShipping_Model_Shipping::METHOD_SEPARATOR, $selectedMethod);
    	return $selectedMethods;
    }
}