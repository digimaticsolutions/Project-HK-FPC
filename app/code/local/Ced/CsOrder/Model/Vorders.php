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
class Ced_CsOrder_Model_Vorders extends Ced_CsMarketplace_Model_Vorders
{
	const STATE_PARTIALLY_PAID =6;
	/**
     * Check vendor order Shipment action availability
     *
     * @return bool
     */
    public function canShip()
    {
        if($this->getOrder()->canShip()){
            foreach ($this->getItemsCollection() as $item) {
                if ($item->getQtyToShip()>0 && !$item->getIsVirtual()
                    && !$item->getLockedDoShip())
                {
                  	return true;
                }
            }
        }
        return false;
    }
	
	/**
     * Retrieve vendor order states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_PAID       => Mage::helper('sales')->__('Paid'),
				self::STATE_PARTIALLY_PAID       => Mage::helper('sales')->__('Partially Paid'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
				self::STATE_REFUND     => Mage::helper('csmarketplace')->__('Refund'),
				self::STATE_REFUNDED   => Mage::helper('csmarketplace')->__('Refunded'),
            );
        }
        return self::$_states;
    }
	/**
     * Check vendor order Invoice action availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        if($this->getOrder()->canInvoice()){
            foreach ($this->getItemsCollection() as $item) {
              if ($item->getQtyToInvoice()>0 && !$item->getLockedDoInvoice()) {
               		return true;
            	}
            }
        }
        return false;
    }
	
	/**
     * Check vendor order Invoice action availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if($this->getOrder()->canCreditmemo()){
	        foreach ($this->getItemsCollection() as $item) {
				if($item->getQtyInvoiced()>$item->getQtyRefunded())
               		return true;
            }
        }
        return false;
    }
	
	
	/**
     * Check vendor order Invoice action availability
     *
     * @return bool
     */
    public function isAdvanceOrder()
    {
        if($this->getVordersMode()>0){
			return true;
        }
		if($this->getVordersMode()==0)
        	return false;
			
		return false;	
    }
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
		public function getOrder($incrementId = false, $viewMode = false){
		$order = parent::getOrder($incrementId);
		
		//if(!$this->canShowShipmentButton() && $viewMode){
		if($this->canShowShipmentButton() && $viewMode){
			$order->setShippingAmount($this->getShippingAmount());
			$order->setBaseShippingAmount($this->getBaseShippingAmount());
			$order->setShippingDescription($this->getShippingDescription());
			$shipping       = $this->getShippingAmount()+$order->getShippingTaxAmount();
			$baseShipping   = $this->getBaseShippingAmount()+$order->getBaseShippingTaxAmount();
			$order->setShippingInclTax($shipping);
			$order->setBaseShippingInclTax($baseShipping);
		}
		return $order;
	}
	
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	public function getVorderByInvoice($invoice){
		
		if($invoice){
			$incrementId = $invoice->getOrder()->getIncrementId();
			$collection = $this->getCollection();
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$collection->addFieldToFilter("vendor_id", $vendorId);
			$collection->addFieldToFilter("order_id", $incrementId);
			return $collection->getFirstItem();
		}
		return false;
	}
	
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	public function getVorderByShipment($shipment){
		
		if($shipment){
			$incrementId = $shipment->getOrder()->getIncrementId();
			$collection = $this->getCollection();
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$collection->addFieldToFilter("vendor_id", $vendorId);
			$collection->addFieldToFilter("order_id", $incrementId);
			return $collection->getFirstItem();
		}
		return false;
	}
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	public function getVorderByCreditmemo($creditmemo){
		
		if($creditmemo){
			$incrementId = $creditmemo->getOrder()->getIncrementId();
			$collection = $this->getCollection();
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$collection->addFieldToFilter("vendor_id", $vendorId);
			$collection->addFieldToFilter("order_id", $incrementId);
			return $collection->getFirstItem();
		}
		return false;
	}
	
	/**
     * Checks shipment allowed or not
     *
     * @return boolean
     */
	public function canShowShipmentButton(){
		//if(!$this->isAdvanceOrder() && $this->getShippingAmount()>0){
		
		if($this->getCode()){
			return true;
		}	
		return false;
	}
	
	
	
	
	
}