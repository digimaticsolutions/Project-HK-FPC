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
class Ced_CsTransaction_Model_Vorder_Items extends Ced_CsTransaction_Model_Items
{
	/**
     * Payment states
     */
    const STATE_READY_TO_PAY       = 1;
	const STATE_PAID       = 2;
    const STATE_READY_TO_REFUND       = 3;
    const STATE_REFUNDED   = 4;
	
    
	protected $_eventPrefix      = 'cstransaction_vorder_items';
    protected $_eventObject      = 'vorder_item';
	protected $_items = null;
	
	protected static $_states;
	/**
     * Initialize resource model
     */
	protected function _construct()
	{
		$this->_init('cstransaction/vorder_items');
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
               /*  self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_PAID       => Mage::helper('sales')->__('Paid'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
				self::STATE_REFUND     => Mage::helper('cstransaction')->__('Refund'),
				self::STATE_REFUNDED   => Mage::helper('cstransaction')->__('Refunded'), */
            );
        }
        return self::$_states;
    }
	
	/**
     * Check vendor order pay action availability
     *
     * @return bool
     */
    public function canPay($vendorId,$orderid)
    {
		
       $collection = Mage::getModel('cstransaction/vorder_items')
							->getCollection()
							->addFieldToFilter('vendor_id',array('eq'=>$vendorId))
							->addFieldToFilter('order_increment_id',array('eq'=>$orderid))
							->addFieldToFilter('qty_ready_to_pay',array('gt'=>0));
		$ids='';
		foreach($collection as $item)
		{
			$ids.=$item->getId().',';
		}
		if($ids=='')
			return false;
		return $ids;
    }
	/**
     * set Qty Ready to Refund
     *
     * 
     */
	public function setQtyForRefund($vorderItem)
	{
		$maxQtyCanBeRefunded=$vorderItem->getQtyPaid()-$vorderItem->getQtyRefunded();
		$pendingQtyToRefund=$vorderItem->getQtyPendingToRefund();
		
		if($maxQtyCanBeRefunded > 0)
		{
			if($pendingQtyToRefund<=$maxQtyCanBeRefunded)
			{
				$vorderItem->setQtyReadyToRefund($pendingQtyToRefund);
			
				$vorderItem->setQtyPendingToRefund(0);
			}
			else
			{
				$vorderItem->setQtyReadytToRefund($maxQtyCanBeRefunded);
				$vorderItem->setQtyPendingToRefund($pendingQtyToRefund-$maxQtyCanBeRefunded);
			
			}
		}
		$vorderItem->save();
		
	}
	
	/**
     * Check vendor order cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getPaymentState() == self::STATE_OPEN;
    }
	
	/**
     * Check vendor order refund action availability
     *
     * @return bool
     */
    public function canMakeRefund()
    {
        return $this->getOrderPaymentState() == Mage_Sales_Model_Order_Invoice::STATE_PAID 
				&& 
			   $this->getPaymentState() == self::STATE_PAID;
    }
	
	/**
     * Check vendor order refund action availability
     *
     * @return bool
     */
    public function canRefund($vendorId,$orderid)
    {
		$collection = Mage::getModel('cstransaction/vorder_items')
							->getCollection()
							->addFieldToFilter('vendor_id',array('eq'=>$vendorId))
							->addFieldToFilter('order_increment_id',array('eq'=>$orderid))
							->addFieldToFilter('qty_ready_to_refund',array('gt'=>0));
		$ids='';
		foreach($collection as $item)
		{
			$ids.=$item->getId().',';
		}
		if($ids=='')
			return false;
		return $ids;
    }
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	 public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        
		$incrementId = $this->getOrderId();
		$vendorId = $this->getVendorId();
		
		$order  = $this->getOrder();
		if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection')
                ->setOrderFilter($order)
				->addFieldToFilter('vendor_id', $vendorId);

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
					if($item->getVendorId() == $vendorId)
	                    $item->setOrder($order);
                }
            }
        }
        return $this->_items;
    }
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	public function getOrder($incrementId = false){
		if(!$incrementId) $incrementId = $this->getOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
		return $order;
		
	}
	
	/**
     * Get Vordered Subtotal
	 * return float
     */
	public function getPurchaseSubtotal(){
		$items = $this->getItemsCollection();
		$subtotal  = 0;
		foreach($items as $_item){
			$subtotal +=$_item->getRowTotal();
		}
		return $subtotal;
	}
	
	/**
	 * Get Vordered base Subtotal
	 * return float
	 */
	public function getBaseSubtotal(){
		$items = $this->getItemsCollection();
		$basesubtotal  = 0;
		foreach($items as $_item){
			$basesubtotal +=$_item->getBaseRowTotal();
		}
		return $basesubtotal;
	}
	
	
	/**
     * Get Vordered Grandtotal
	 * return float
     */
	public function getPurchaseGrandTotal(){
		$items = $this->getItemsCollection();
		$grandtotal  = 0;
		foreach($items as $_item){
			$grandtotal +=$_item->getRowTotal()+ $_item->getTaxAmount()+ $_item->getHiddenTaxAmount()+ $_item->getWeeeTaxAppliedRowAmount()- $_item->getDiscountAmount();
		}
		return $grandtotal;
	}
	
	/**
	 * Get Vordered base Grandtotal
	 * return float
	 */
	public function getBaseGrandTotal(){
		$items = $this->getItemsCollection();
		$basegrandtotal  = 0;
		foreach($items as $_item){
			$basegrandtotal +=$_item->getBaseRowTotal()+ $_item->getBaseTaxAmount() + $_item->getBaseHiddenTaxAmount() + $_item->getBaseWeeeTaxAppliedRowAmount() - $_item->getBaseDiscountAmount();
		}
		return $basegrandtotal;
	}
	
	
	
	/**
	 * Get Vordered tax
	 * return float
	 */
	public function getPurchaseTaxAmount(){
		$items = $this->getItemsCollection();
		$tax  = 0;
		foreach($items as $_item){
			$tax +=$_item->getTaxAmount()+ $_item->getHiddenTaxAmount()+ $_item->getWeeeTaxAppliedRowAmount();
		}
		return $tax;
	}
	
	/**
	 * Get Vordered tax
	 * return float
	 */
	public function getBaseTaxAmount(){
		$items = $this->getItemsCollection();
		$tax  = 0;
		foreach($items as $_item){
			$tax +=$_item->getBaseTaxAmount()+ $_item->getBaseHiddenTaxAmount()+ $_item->getBaseWeeeTaxAppliedRowAmount();
		}
		return $tax;
	}
	
	/**
	 * Get Vordered Discount
	 * return float
	 */
	public function getPurchaseDiscountAmount(){
		$items = $this->getItemsCollection();
		$discount  = 0;
		foreach($items as $_item){
			$discount +=$_item->getDiscountAmount();
		}
		return $discount;
	}
	
	/**
	 * Get Vordered Discount
	 * return float
	 */
	public function getBaseDiscountAmount(){
		$items = $this->getItemsCollection();
		$discount  = 0;
		foreach($items as $_item){
			$discount +=$_item->getBaseDiscountAmount();
		}
		return $discount;
	}
	
	/**
	 * Calculate the commission fee
	 *
	 * @return Ced_CsTransaction_Model_Vorder_Items
	 */
	public function collectCommission() {
		if ($this->getData('vendor_id') && $this->getData('base_to_global_rate') && $this->getData('order_total')) {
			$order = $this->getOrder();
			$helper = Mage::helper('cstransaction/acl')->setStoreId($order->getStoreId())->setOrder($order);
			$commissionSetting = $helper->getCommissionSettings($this->getData('vendor_id'));
			$commission = $helper->calculateCommission($this->getData('order_total'),$this->getData('base_order_total'),$this->getData('base_to_global_rate'),$commissionSetting) ;
			$this->setShopCommissionTypeId($commissionSetting['type']);
			$this->setShopCommissionRate($commissionSetting['rate']);
			$this->setShopCommissionBaseFee($commission['base_fee']);
			$this->setShopCommissionFee($commission['fee']);
			$this->setPaymentState(self::STATE_OPEN);
			$this->setOrderPaymentState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
		}
	}
	
}

?>