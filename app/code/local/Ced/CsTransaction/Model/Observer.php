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
 
Class Ced_CsTransaction_Model_Observer
{
	/**
	 * Saving vorder items after vorder save
	 */
	public function vorderSaveAfter($observer){
	
		try{
			
			if($observer->getVorder()->getId() && Mage::helper('csorder')->isActive())
			{
				
				$vorder=Mage::getModel('csmarketplace/vorders')->load($observer->getVorder()->getId());
				$itemsFee=json_decode($vorder->getItemsCommission(),true);
				
				$order = Mage::getModel('sales/order')->load($vorder->getOrderId(), 'increment_id');
				foreach($order->getAllItems() as $item)
				{
					if ($item->getParentItem()) continue;
					$existingItem=Mage::getModel('cstransaction/vorder_items')
								->getCollection()
								->addFieldToFilter('order_item_id',array('eq'=>$item->getId()))
								->getFirstItem();
					if(!$existingItem->getId()&& $item->getVendorId()==$vorder->getVendorId())
					{
						$vorderItem=Mage::getModel('cstransaction/vorder_items');
						$vorderItem->setParentId($observer->getVorder()->getId());
						$vorderItem->setOrderItemId($item->getId());
						$vorderItem->setOrderId($order->getId());
						$vorderItem->setOrderIncrementId($order->getIncrementId());
						$vorderItem->setVendorId($vorder->getVendorId());
						$vorderItem->setCurrency($vorder->getCurrency());
						$vorderItem->setBaseRowTotal($item->getBaseRowTotal());
						$vorderItem->setRowTotal($item->getRowTotal());
						$vorderItem->setSku($item->getSku());						
						$vorderItem->setShopCommissionTypeId($vorder->getShopCommissionTypeId());
						$vorderItem->setShopCommissionRate($vorder->getShopCommissionRate());
						$vorderItem->setShopCommissionBaseFee($vorder->getShopCommissionBaseFee());
						$vorderItem->setShopCommissionFee($item->getShopCommissionFee());
						$vorderItem->setProductQty($item->getQtyOrdered());
						$vorderItem->setItemPaymentState(0);
						
						$vorderItem->setItemFee(($item->getRowTotal()-$itemsFee[$item->getId()]['fee'])/$item->getQtyOrdered());
						$vorderItem->setItemCommission($itemsFee[$item->getId()]['fee']/$item->getQtyOrdered());
						$vorderItem->save();
					}
					
							
				}
				
				
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
	}
	/**
	 * Saving updating items payment details before transaction save
	 */
	public function vpaymentsSaveBefore($observer){
	
		try{
			if(Mage::helper('csorder')->isActive()){
				if(!$observer->getVpayment()->getId())
				{
					$data=Mage::app()->getRequest()->getPost();
					$vpayment=$observer->getVpayment();
					
					/* print_r($vpayment->getData());die; */
					$type=$vpayment->getData('transaction_type');
					if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {		
						if(isset($data['amount_desc']))
						{
							if($vpayment->getData('item_wise_amount_desc'))
							{
								$amountDesc=json_decode($vpayment->getData('item_wise_amount_desc'),true);
							
								foreach($amountDesc as $orderId=>$vorderItems)
								{
									foreach($vorderItems as $itemId=>$amount)
									{
										$vorderItem=Mage::getModel('cstransaction/vorder_items')->load($itemId);
										$vorderItem->setAmountRefunded($vorderItem->getAmountRefunded()+$amount);
										$qty=(int)($amount/$vorderItem->getItemFee());
										$vorderItem->setQtyRefunded($vorderItem->getQtyRefunded()+$qty);
										$vorderItem->setQtyReadyToRefund($vorderItem->getQtyReadyToRefund()-$qty);
										$vorderItem->save();
										
									}
											
								}
							}
						
						
						}
					}
					else
					{
						$orderPaidQty=array();
						if(isset($data['amount_desc']))
						{
							if($vpayment->getData('item_wise_amount_desc'))
							{
								$amountDesc=json_decode($vpayment->getData('item_wise_amount_desc'),true);
							
								foreach($amountDesc as $orderId=>$vorderItems)
								{
									foreach($vorderItems as $itemId=>$amount)
									{
										
										$vorderItem=Mage::getModel('cstransaction/vorder_items')->load($itemId);
										$vorderItem->setAmountPaid($vorderItem->getAmountPaid()+$amount);
										$qty=(int)($amount/$vorderItem->getItemFee());
										$vorderItem->setQtyPaid($vorderItem->getQtyPaid()+$qty);
										$vorderItem->setQtyReadyToPay($vorderItem->getQtyReadyToPay()-$qty);
										$vorderItem->save();
										if(isset($orderPaidQty[$orderId]))
										{
											$orderPaidQty[$orderId]['qty_paid']+=$vorderItem->getQtyPaid();
											$orderPaidQty[$orderId]['qty_ordered']+=$vorderItem->getProductQty();
											
											
										}
										else
										{
											$orderPaidQty[$orderId]['qty_paid']=$vorderItem->getQtyPaid();
											$orderPaidQty[$orderId]['qty_ordered']=$vorderItem->getProductQty();
											$orderPaidQty[$orderId]['parent_id']=$vorderItem->getParentId();
										}
										$vorderItem->setQtyForRefund($vorderItem);
									}
											
								}
								foreach($orderPaidQty as $vorderId=>$details)
								{
									if($details['qty_paid']==$details['qty_ordered'])
									{
										$vorder=Mage::getModel('csmarketplace/vorders')->load($vorderId);
										$vorder->setPaymentState(Ced_CsMarketplace_Model_Vorders::STATE_PAID);
										$vorder->save();
									}
									else
									{
										$vorder=Mage::getModel('csmarketplace/vorders')->load($vorderId);
										$vorder->setPaymentState(Ced_CsOrder_Model_Vorders::STATE_PARTIALLY_PAID);
										$vorder->save();
									}
								}
							}
						
						
						}
					}
				}
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
	}
	/**
	 *Change Item Payment State on invoice
	 *
	 */
	public function prepareItemsForPayment($observer) {
		if(Mage::helper('csorder')->isActive()){
			$invoice = $observer->getDataObject();
			$order = $invoice->getOrder();
			Mage::helper('csmarketplace')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_PAYMENT_STATE_CHANGED);
			
			
							
			$vendorId=false;
			
			foreach ($invoice->getAllItems() as $item) {
				try{
						if ($item->getParentItem()) continue;
						$vorderItem=Mage::getModel('cstransaction/vorder_items')
									->getCollection()
									->addFieldToFilter('order_item_id',array('eq'=>$item->getOrderItemId()))
									->getFirstItem();
						$vorderItem->setItemPaymentState(Ced_CsTransaction_Model_Vorder_Items::STATE_READY_TO_PAY);
						$vorderItem->setQtyReadyToPay($vorderItem->getQtyReadyToPay()+$item->getQty());
						$vorderItem->setTotalInvoicedAmount($vorderItem->getTotalInvoicedAmount()+$item->getRowTotal());
						$vorderItem->save();
						$vendorId=$vorderItem->getVendorId();
						

					}
					catch(Exception $e){
					Mage::helper('csmarketplace')->logException($e);
					}
			}
			if($vendorId){
				$vorder = Mage::getModel('cstransaction/vorder_items')
							->getCollection()
							->addFieldToFilter('order_increment_id',array('eq'=>$order->getIncrementId()))
							->addFieldToFilter('vendor_id',array('eq'=>$vendorId))
							->getFirstItem()
							;
			}
								 
			
			
			return $this;
		}
		//$invocies = $order->getInvoiceCollection(); 
	}
		
		/**
		 * Set items for refund
		 *
		 */
		public function prepareItemsForRefund($observer){
			if(Mage::helper('csorder')->isActive()){
				$creditmemo = $observer->getCreditmemo();
				$creditmemoVendor = array();
				$vendorId=false;
			
				foreach ($creditmemo->getAllItems() as $item) {
					try{
							if ($item->getParentItem()) continue;
							$vorderItem=Mage::getModel('cstransaction/vorder_items')
										->getCollection()
										->addFieldToFilter('order_item_id',array('eq'=>$item->getOrderItemId()))
										->getFirstItem();
							$vorderItem->setQtyPendingToRefund($vorderItem->getQtyPendingToRefund()+$item->getQty());
							$qtyReadyToPay=$vorderItem->getQtyReadyToPay()-$item->getQty();
							$vorderItem->setQtyReadyToPay($qtyReadyToPay<0?0:$qtyReadyToPay);
							$vorderItem->setTotalCreditmemoAmount($vorderItem->getTotalCreditmemoAmount()+$item->getRowTotal());
							$vorderItem->save();
							$vendorId=$vorderItem->getVendorId();
							$vorderItem->setQtyForRefund($vorderItem);

						}
						catch(Exception $e){
						Mage::helper('csmarketplace')->logException($e);
						}
				}
			}
			
		}
}