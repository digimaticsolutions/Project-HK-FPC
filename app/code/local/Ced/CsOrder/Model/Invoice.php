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
class Ced_CsOrder_Model_Invoice extends Ced_CsMarketplace_Model_Abstract
{

	/**
     * Constructor
     *
     */
	protected function _construct()
	{
		$this->_init('csorder/invoice');
	}
	
	
	/**
     * Update Total
     *
     * @return object
     */
	public function updateTotal($invoice, $view=true){
		  
			$vorder = Mage::getModel("csmarketplace/vorders")->getVorderByInvoice($invoice);
			
			if(!is_object($vorder))
				return $invoice;
			
			
			if(!$vorder->isAdvanceOrder() && $vorder->getCode()){
				$invoice->setOrder($vorder->getOrder(false, true));
				$shippingCost = 0;
				//if($invoice->getShippingAmount()>0){
				if($view && $vInvoice = $this->updateInvoiceGridTotal($invoice)){
					$invoice->setShippingAmount($vInvoice->getShippingAmount());
					$invoice->setBaseShippingAmount($vInvoice->getBaseShippingAmount());
				}else if($this->canInvoiceIncludeShipment($invoice)){
					$invoice->setShippingAmount($vorder->getShippingAmount());
					$invoice->setBaseShippingAmount($vorder->getShippingAmount());
				}
				
				$subtotal = $this->getItemSubtotalByInvoice($invoice);
				$invoice->setSubtotal($subtotal);
				$tax = $this->getItemTaxByInvoice($invoice);
				$invoice->setTaxAmount($tax);
				$invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
			}

			if(!Mage::helper('csorder')->canShowShipmentBlock($vorder)){
			 	$invoice->setShippingAmount(0);
				$invoice->setBaseShippingAmount(0);
				$subtotal = $this->getItemSubtotalByInvoice($invoice);
				$invoice->setSubtotal($subtotal);
				$tax = $this->getItemTaxByInvoice($invoice);
				$invoice->setTaxAmount($tax);


				$invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
			 }
			
			return $invoice;
	}
	
	
	/**
     * Update Total
     *
     * @return object
     */
	public function updateTotalGrid($invoice){
		  
			$vorder = Mage::getModel("csmarketplace/vorders")->getVorderByInvoice($invoice);
			
			if(!is_object($vorder))
				return $invoice;
			
			
			if(!$vorder->isAdvanceOrder() && $vorder->getCode()){
				$invoice->setOrder($vorder->getOrder(false, true));
				$shippingCost = 0;
				if($vInvoice = $this->updateInvoiceGridTotal($invoice)){
					$invoice->setShippingAmount($vInvoice->getShippingAmount());
					$invoice->setBaseShippingAmount($vInvoice->getBaseShippingAmount());
				}
				$subtotal = $this->getItemSubtotalByInvoice($invoice);
				$invoice->setSubtotal($subtotal);
				$tax = $this->getItemTaxByInvoice($invoice);
				$invoice->setTaxAmount($tax);
				$invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
			}

			if(!Mage::helper('csorder')->canShowShipmentBlock($vorder)){
			 	$invoice->setShippingAmount(0);
				$invoice->setBaseShippingAmount(0);
				$subtotal = $this->getItemSubtotalByInvoice($invoice);
				$invoice->setSubtotal($subtotal);
				$tax = $this->getItemTaxByInvoice($invoice);
				$invoice->setTaxAmount($tax);


				$invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
			 }
			
			return $invoice;
	}
	
	
	/**
     * Getting Item Subtotal By Invoice
     *
     * @return decimal
     */
	public function getItemSubtotalByInvoice($invoice){
			$items = $invoice->getAllItems();
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
     * Getting Item Tax By Invoice
     *
     * @return decimal
     */
	public function getItemTaxByInvoice($invoice){
			$items = $invoice->getAllItems();
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
	
	/**
     * Getting Can include shipping amount Invoice
     *
     * @return boolean
     */
	public function canInvoiceIncludeShipment($invoice){
		if(is_object($invoice)){
		$vendorId = Mage::getSingleton('customer/session')->getVendorId();
		$invoicedCollection = $this->getCollection()
								->addFieldTofilter('invoice_order_id',$invoice->getOrderId())
								->addFieldTofilter('vendor_id',$vendorId)
								->addFieldTofilter('shipping_code', array('notnull' => true));
			if(count($invoicedCollection)==0)
				return true;
		}
		return false;						
	}
	
	/**
     * Getting Can include shipping amount Invoice
     *
     * @return boolean
     */
	public function updateInvoiceGridTotal($invoice){
		if(is_object($invoice)){
		$vendorId = Mage::getSingleton('customer/session')->getVendorId();
		$invoicedCollection = $this->getCollection()
								->addFieldTofilter('invoice_id',$invoice->getId())
								->addFieldTofilter('vendor_id',$vendorId)
								->addFieldTofilter('shipping_code', array('notnull' => true));
			if(count($invoicedCollection)>0)
				return $invoicedCollection->getFirstItem();
		}
		return false;						
	}
}