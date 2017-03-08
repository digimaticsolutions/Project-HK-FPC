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
class Ced_CsOrder_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected $_isSplitOrder = false;
	/**
	 * get vendor name by address
	 */
	public function getVendorNameByAddress($address) {
		
		if (is_numeric($address)) {
           $address=Mage::getModel('customer/address')->load($address);
			if($address->getVendorId())
			{
				$vendor=Mage::getModel('csmarketplace/vendor')->load($address->getVendorId());
				return $vendor->getName();
			}
			else
			{
				return 'Admin';
			}
        } elseif ($address && $address->getId()) {
			$vendor=Mage::getModel('csmarketplace/vendor')->load($address->getVendorId());
			return $vendor->getName();
		}
		
	}
	/**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorLogEnabled()
    {
			return Mage::getStoreConfig('ced_csmarketplace/vlogs/active',$this->getStore()->getId());
    }

	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStore() {
		$storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        if($storeId)
			return Mage::app()->getStore($storeId);
		else 
			return Mage::app()->getStore();
	 }
	 /**
	 * Log Process Data
	 */
	 public function logProcessedData($data, $tag=false) {
	 

	 	if(!$this->isVendorLogEnabled())
			return;
			
		$file = Mage::getStoreConfig('ced_vlogs/general/process_file');
				
		$controller = Mage::app()->getRequest()->getControllerName();
		$action = Mage::app()->getRequest()->getActionName();
		$router = Mage::app()->getRequest()->getRouteName();
		$module = Mage::app()->getRequest()->getModuleName();
		
		$out = '';
		//if ($html) 
		@$out .= "<pre>";
		@$out .= "Controller: $controller\n";
		@$out .= "Action: $action\n";
		@$out .= "Router: $router\n";
		@$out .= "Module: $module\n";
		foreach(debug_backtrace() as $key=>$info)
        {
            @$out .= "#" . $key . " Called " . $info['function'] ." in " . $info['file'] . " on line " . $info['line']."\n"; 
			break;        
        }
		if($tag)
			@$out .= "#Tag " . $tag."\n"; 
			
		//if ($html)
		@$out .= "</pre>";
		Mage::log("\n Source: \n" . print_r($out, true), Zend_Log::INFO, $file, true);
		Mage::log("\n Processed Data: \n" . print_r($data, true), Zend_Log::INFO, $file, true);
	 }
	 
	 
	 /**
	 * Log Exception
	 */
	 public function logException(Exception $e) {
	 	if(!$this->isVendorLogEnabled())
			return;
			
		$file = Mage::getStoreConfig('ced_vlogs/general/exception_file');
        Mage::log("\n" . $e->__toString(), Zend_Log::ERR, $file, true);
		
	 }
	 
	 /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorDebugEnabled()
    {
    	$isDebugEnable = (int)Mage::getStoreConfig('ced_csmarketplace/vlogs/debug_active');
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if( $isDebugEnable ){
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps = Mage::getStoreConfig('dev/restrict/allow_ips');
            if ( $isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {
                    $allow = false;
                }
            }
        }

        return $allow;
    
	}
	
	
	
	/**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isSplitOrderEnabled()
    {
    	$this->_isSplitOrder = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_mode');
        return $this->_isSplitOrder;
	}
	
	/**
     * Check Split Order is enabled
     *
     * @return boolean
     */
    public function canCreateInvoiceEnabled($vorder)
    {

		//if(!Mage::app()->getStore()->isAdmin() && $vorder->isAdvanceOrder() && $this->isSplitOrderEnabled()){
		if(!Mage::app()->getStore()->isAdmin()){
			$isSplitOrderEnable = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_caninvoice');
			return $isSplitOrderEnable;
		}
		return false;
	}
	
	/**
     * Check Can Create Shipment is enabled
     *
     * @return boolean
     */
    public function canCreateShipmentEnabled($vorder)
    {
		
		if(!Mage::app()->getStore()->isAdmin() && $vorder->canShowShipmentButton()){
			$isSplitOrderEnable = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_canshipment');
			return $isSplitOrderEnable;
		}
		/*
		if($vorder->isAdvanceOrder() && $this->isSplitOrderEnabled()){
			$isSplitOrderEnable = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_canshipment');
			return $isSplitOrderEnable;
		}*/
		return false;
			
	}
	
	/**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function canCreateCreditmemoEnabled($vorder)
    {
		//if(!Mage::app()->getStore()->isAdmin() && $vorder->isAdvanceOrder() && $this->isSplitOrderEnabled()){
		if(!Mage::app()->getStore()->isAdmin()){
			$isSplitOrderEnable = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_cancreditmemo');
			return $isSplitOrderEnable;
		}
		return false;	
	}
	
	
	/**
     * Check Can distribute shipment
     *
     * @return boolean
     */
	/*
    public function canEqualyDistributeShipment()
    {
		
		if(!$this->isSplitOrderEnabled()){
			$distribteShipment = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_shipment_rule');
			return $distribteShipment;
		}
		return false;
			
	}*/
	
	
	/**
     * Check Can show Shipment shipment
     *
     * @return boolean
     */
    public function canShowShipmentBlock($vorder)
    {
    	if($vorder->getCode()==null)
			return false;
		return true;
	}
	
	/**
     * Check Can distribute shipment
     *
     * @return boolean
     */
	
    public function isActive()
    {
		return (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_active');
	}
	
	
    
}