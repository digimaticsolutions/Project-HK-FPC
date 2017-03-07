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
class Ced_CsOrder_Model_Creditmemo extends Ced_CsMarketplace_Model_Abstract
{
	/**
     * Constructor
     *
     */
	protected function _construct()
	{
		$this->_init('csorder/creditmemo');
	}
	
	/**
     * Update Total
     *
     * @return object
     */	
	public function updateTotal($creditmemo){
		 
			$vorder = Mage::getModel("csmarketplace/vorders")->getVorderByCreditmemo($creditmemo);
			if(!$vorder->isAdvanceOrder() && $vorder->getShippingAmount()>0){
				$creditmemo->setOrder($vorder->getOrder(false, true));
				$shippingCost = 0;
				if($creditmemo->getShippingAmount()>0){
					$creditmemo->setShippingAmount($vorder->getShippingAmount());
					$creditmemo->setBaseShippingAmount($vorder->getShippingAmount());

				}
				$subtotal = $this->getItemSubtotalByCreditmemo($creditmemo);
				$creditmemo->setSubtotal($subtotal);
				$tax = $this->getItemTaxByCreditmemo($creditmemo);
				$creditmemo->setTaxAmount($tax);

				$adjustmentPositive = $creditmemo->getAdjustmentPositive();
				$adjustment = $creditmemo->getAdjustment();
				$grandTotal = $subtotal 
								+ $tax 
								//+ $adjustmentPositive
								+ $adjustment
								+ $creditmemo->getShippingAmount();
								


				$creditmemo->setGrandTotal($grandTotal);
				
			}	
			
			
			if(!Mage::helper('csorder')->canShowShipmentBlock($vorder)){
			 	$creditmemo->setShippingAmount(0);
				$creditmemo->setBaseShippingAmount(0);
				$subtotal = $this->getItemSubtotalByCreditmemo($creditmemo);
				$creditmemo->setSubtotal($subtotal);
				$tax = $this->getItemTaxByCreditmemo($creditmemo);
				$creditmemo->setTaxAmount($tax);
				
				$adjustmentPositive = $creditmemo->getAdjustmentPositive();
				$adjustment = $creditmemo->getAdjustment();
				$grandTotal = $subtotal 
								+ $tax 
								//+ $adjustmentPositive
								+ $adjustment
								+ $creditmemo->getShippingAmount();
								


				$creditmemo->setGrandTotal($grandTotal);
			 }
			
			
			
			return $creditmemo;
	}
	
	/**
     * Calculating Items Subtotal By Credit Memo
     *
     * @return decimal
     */
	public function getItemSubtotalByCreditmemo($creditmemo){
			$items = $creditmemo->getAllItems();
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$total = 0;
			foreach($items as $_item){
				$vendorIdProductId = Mage::getModel('csmarketplace/vproducts')
				->getVendorIdByProduct($_item->getProductId());
				if ($_item->getOrderItem()->getParentItem() || $vendorIdProductId!=$vendorId ) continue;
				$total += $_item->getRowTotal();
			}
			return $total;
	}

	/**
     * Calculating Items Tax By Credit Memo
     *
     * @return decimal
     */
	public function getItemTaxByCreditmemo($creditmemo){
			$items = $creditmemo->getAllItems();
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$total = 0;
			foreach($items as $_item){
				$vendorIdProductId = Mage::getModel('csmarketplace/vproducts')
				->getVendorIdByProduct($_item->getProductId());
				if ($_item->getOrderItem()->getParentItem() || $vendorIdProductId!=$vendorId ) continue;
				$total += $_item->getTaxAmount();
			}
			return $total;
	}
	
}