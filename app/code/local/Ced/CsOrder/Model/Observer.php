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
class Ced_CsOrder_Model_Observer extends Ced_CsMarketplace_Model_Observer
{
	/**
     * Sent Vendor Sales Order After Order Place
     *
     */
	public function setVendorSalesOrder($observer)
	{
	
		if(Mage::helper('csorder')->isActive()){
			$order = $observer->getOrder();
			$vorder=Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',$order->getIncrementId())->getFirstItem();
			if($vorder->getId())
			{
				return $this;
			}
			$quote=Mage::getSingleton('checkout/session')->getQuote();
			
			$isMultiShipping=false;
			if($quote->getIsMultiShipping())
				$isMultiShipping=true;
			
			
			if($order)
			{
				$orders=array($order);
			}
			else
			{
				$orders = $observer->getOrders();
				
				
			}
			foreach($orders as $order)
			{
				
				
				$products = $order->getAllItems();
				$baseToGlobalRate=$order->getBaseToGlobalRate()?$order->getBaseToGlobalRate():1;
				$vendorsBaseOrder = array();
				$vendorQty = array();
				

				Mage::helper('csorder')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_CREATE);
				 foreach ($products as $item) {
					$vendorId = Mage::getModel("csmarketplace/vproducts")->getVendorIdByProduct($item->getProductId());
					
					if((int)$vendorId==0)
					{	
						continue;
					}

				   $price = 0;
				   
					$price = $item->getBaseRowTotal()
							+ $item->getBaseTaxAmount()
							+ $item->getBaseHiddenTaxAmount()
							+ $item->getBaseWeeeTaxAppliedRowAmount()
							- $item->getBaseDiscountAmount();
					$vendorsBaseOrder[$vendorId]['order_total'] = isset($vendorsBaseOrder[$vendorId]['order_total'])?($vendorsBaseOrder[$vendorId]['order_total'] + $price) : $price;
					$vendorsBaseOrder[$vendorId]['item_commission'][$item->getId()] = $price;									;
					$vendorsBaseOrder[$vendorId]['order_items'][] = $item;
					$vendorQty[$vendorId] = isset($vendorQty[$vendorId])?$vendorQty[$vendorId] + $item->getQty() :  $item->getQty();
				   $item->setVendorId($vendorId)->save();
				   $logData = $item->getData();
				   unset($logData['product']);
				   Mage::helper('csorder')->logProcessedData($logData, Ced_CsMarketplace_Helper_Data::SALES_ORDER_ITEM);
				 }
				
				
				 
				foreach($vendorsBaseOrder  as $vendorId => $baseOrderTotal){
				 try{	
						//$baseShippingAmount=$order->getShippingAmount();
						/* $vendorShipping=Mage::helper('directory')->currencyConvert($baseShippingAmount/count($vendorsBaseOrder), $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode()); */
						
						//$baseOrderTotal['order_total']=$isMultiShipping?$baseOrderTotal['order_total']+$baseShippingAmount:$baseOrderTotal['order_total'];//commented for split order
						
						$qty = isset($vendorQty[$vendorId])? $vendorQty[$vendorId] : 0;
						$vorder = Mage::getModel('csmarketplace/vorders');
						$vorder->setVendorId($vendorId);
						$vorder->setOrder($order);
						$vorder->setOrderId($order->getIncrementId());
						$vorder->setCurrency($order->getGlobalCurrencyCode());
						
						Mage::dispatchEvent('ced_csmarketplce_vorder_shipping_save_before',array('vorder'=>$vorder));
						
						$base_order_total =  $base_order_total_pre =$baseOrderTotal['order_total'];
						$order_total = $order_total_pre = Mage::helper('directory')->currencyConvert($baseOrderTotal['order_total'], $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
						
						$baseShippingAmount = $vorder->getBaseShippingAmount()?$vorder->getBaseShippingAmount():0;
						$shippingAmount = Mage::helper('directory')->currencyConvert($baseShippingAmount, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
						
				if(Mage::getStoreConfig('ced_vpayments/general/shipping_commissionable',Mage::app()->getStore()->getId())){		
						$order_total += $shippingAmount;
						$base_order_total +=  $baseShippingAmount;
				}		
		
						$vorder->setOrderTotal($order_total);
						$vorder->setBaseOrderTotal($base_order_total);

						$vorder->setBaseCurrency($order->getBaseCurrencyCode());
						$vorder->setBaseToGlobalRate($baseToGlobalRate);
						$vorder->setProductQty($qty);


						$vorder->setItemCommission($baseOrderTotal['item_commission']);
						$vorder->collectCommission();

						$vorder->setOrderTotal($order_total_pre + $baseShippingAmount);
						$vorder->setBaseOrderTotal($base_order_total_pre + $baseShippingAmount);
						
						$vorder->setVordersMode(Mage::getStoreConfig('ced_vorders/general/vorders_mode',Mage::app()->getStore()->getId()));
						
						/*$vorder->setBaseShippingAmount($baseShippingAmount);
						
						if(Mage::getStoreConfig('ced_vorders/general/vorders_mode',Mage::app()->getStore()->getId())){
							
							if($vorder->getShippingCode()){
								$vorder->setShippingAmount($vendorShipping);
								$vorder->setShippingDescription($order->getShippingDescription());
								$vorder->setBillingCountryCode($order->getBillingAddress()->getData('country_id'));
								if($order->getShippingAddress())
									$vorder->setShippingCountryCode($order->getShippingAddress()->getData('country_id'));
							}
						}*/
						$vorder->save();
						

					}
					catch(Exception $e){
						echo $e->getMessage();die;
						Mage::helper('csorder')->logException($e);
					}
				}
				
			}
		}
		else
		{
			parent::setVendorSalesOrder($observer);
		}
	}

		
	/**
     * Create Vendor Shipment
     *
     */	
	public function createVendorShipment($observer){ 
		if(Mage::helper('csorder')->isActive()){
			$shipment = $observer->getShipment();
			$allItems = $shipment->getAllItems();
			$shipmentVendor = array();
			
			foreach($allItems as $item){
				$vendorId = Mage::getModel("csmarketplace/vproducts")->getVendorIdByProduct($item->getProductId());
				$shipmentVendor[$vendorId] = $vendorId;
			}
			foreach($shipmentVendor as $vendorId){
				try{
					$id = $shipment->getId();
					$vshipment = Mage::getModel('csorder/shipment');
					$vshipment->setShipmentId($id);
					$vshipment->setVendorId($vendorId);
					$vshipment->save();
				}catch(Exception $e){
					$e->getMessage();
				}
			}
		}
	}
	
	/**
     * Create Vendor Invoice
     *
     */	
	public function createVendorInvoice($observer){
		if(Mage::helper('csorder')->isActive()){
			$invoice = $observer->getInvoice();
			$allItems = $invoice->getAllItems();
			$invoiceVendor = array();
			
			

			
										
										
										
			foreach($allItems as $item){
				$vendorId = Mage::getModel("csmarketplace/vproducts")->getVendorIdByProduct($item->getProductId());
				$invoiceVendor[$vendorId] = $vendorId;
			}
			foreach($invoiceVendor as $vendorId){
				
				$vInvoice = Mage::getModel('csorder/invoice');
				try{
					$id = $invoice->getId();
					$vInvoice->setInvoiceId($id);
					$vInvoice->setVendorId($vendorId);
					$vInvoice->setInvoiceOrderId($invoice->getOrderId());
					if($vInvoice->canInvoiceIncludeShipment($invoice)){
						$vorder = Mage::getModel("csmarketplace/vorders")->getVorderByInvoice($invoice);
						$vInvoice->setShippingCode($vorder->getCode());
						$vInvoice->setShippingDescription($vorder->getShippingDescription());
						$vInvoice->setBaseShippingAmount($vorder->getBaseShippingAmount());
						$vInvoice->setShippingAmount($vorder->getShippingAmount());
					}
					$vInvoice->save();
				}catch(Exception $e){
					$e->getMessage();
				}
			}
		}
	}
	
	/**
     * Create Vendor Credit Memo
     */
	public function createVendorCreditmemo($observer){
		if(Mage::helper('csorder')->isActive()){
			$creditmemo = $observer->getCreditmemo();
			$allItems = $creditmemo->getAllItems();
			$creditmemoVendor = array();
			
			foreach($allItems as $item){
				$vendorId = Mage::getModel("csmarketplace/vproducts")->getVendorIdByProduct($item->getProductId());
				$creditmemoVendor[$vendorId] = $vendorId;
			}
			
			foreach($creditmemoVendor as $vendorId){
				try{
					$id = $creditmemo->getId();
					$vCreditmemo = Mage::getModel('csorder/creditmemo');
					$vCreditmemo->setCreditmemoId($id);
					$vCreditmemo->setVendorId($vendorId);
					$vCreditmemo->save();
				}catch(Exception $e){
					$e->getMessage();
				}
			}
		}
	}
	
	/**
     * 	Checking Onepage Controller View Is Allowed Or Not
     *	Checking Cs Order Checkout Is Allowed Or Not
     */
	public function controllerActionLayoutLoadBefore(Varien_Event_Observer $observer)
	{
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$cartItems = $quote->getAllVisibleItems();
		$onlyVirtual=true;
		foreach ($cartItems as $item) {
			if($item->getProduct()->getTypeId()!='virtual'&& $item->getProduct()->getTypeId()!='downloadable'){
				$onlyVirtual=false;
				break;
			}
		}
		if(!$onlyVirtual){
			
			if(Mage::helper('csorder')->isActive()){
				
				
			
					if(get_class($observer->getAction())=='Mage_Checkout_OnepageController')
					{
						if(Mage::getStoreConfig('ced_vorders/general/vorders_mode',Mage::app()->getStore()->getId())) 
						{
							/* Mage::getSingleton('checkout/session')->addError('Requested action is not allowed..'); */
							Mage::app()->getResponse()->setRedirect(Mage::getUrl("csorder/multishipping",array('_secure'=>true)));
							return;
						}
					}
					
					if(get_class($observer->getAction())=='Mage_Checkout_MultishippingController' && Mage::app()->getRequest()->getActionName()=='addresses')
					{
						if(Mage::getStoreConfig('ced_vorders/general/vorders_mode',Mage::app()->getStore()->getId())) 
						{
							/* Mage::getSingleton('checkout/session')->addError('Requested action is not allowed..'); */
							Mage::app()->getResponse()->setRedirect(Mage::getUrl("csorder/multishipping/addresses",array('_secure'=>true)));
							return;
						}
					}
						
					
					if(get_class($observer->getAction())=='Ced_CsOrder_CartController')
					{
						
						if(!Mage::getStoreConfig('ced_vorders/general/vorders_mode',Mage::app()->getStore()->getId())) 
						{

							$layout = $observer->getEvent()->getLayout();
				 
							$layout->getUpdate()->removeHandle('csorder_cart_index');
				 
							$layout->getUpdate()->addHandle('checkout_cart_index');
						}
					}
				
			}
			else
			{
				if(get_class($observer->getAction())=='Ced_CsOrder_CartController')
				{
					
					if(!Mage::getStoreConfig('ced_vorders/general/vorders_mode',Mage::app()->getStore()->getId())) 
					{

						$layout = $observer->getEvent()->getLayout();
			 
						$layout->getUpdate()->removeHandle('csorder_cart_index');
			 
						$layout->getUpdate()->addHandle('checkout_cart_index');
					}
				}
			}
		}
	}
	public function actionPredispatch($observer)
    {
        //we compare action name to see if that's action for which we want to add our own event
		
        if($observer->getEvent()->getControllerAction()->getFullActionName() == 'csorder_multishipping_index') 
        {
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			$cartItems = $quote->getAllVisibleItems();
			$onlyVirtual=true;
			foreach ($cartItems as $item) {
				if($item->getProduct()->getTypeId()!='virtual'&& $item->getProduct()->getTypeId()!='downloadable'){
					$onlyVirtual=false;
					break;
				}
			}
			if($onlyVirtual)
			{
			
				header('Location:'.Mage::getUrl("checkout/onepage",array('_secure'=>true)));
				
				die;
			}
            
        }
    }
	
	/**
     * Set Vendor As Custom Option To Item
     *
     */
	public function setVendorToItem(Varien_Event_Observer $observer)
	{
		if(Mage::helper('csorder')->isActive() ){
			
			$item = $observer->getQuoteItem();
			$product = $item->getProduct();
			if($product->getTypeId()=='configurable')
				return $this;
			$vendorId = Mage::getModel("csmarketplace/vproducts")->getVendorIdByProduct($product);
			$options = Mage::helper('catalog/product_configuration')->getCustomOptions($item);
			$addOption=true;
			$customOptions=array();
			if($vendorId){
				$vendor=Mage::getModel("csmarketplace/vendor")->load($vendorId );
				foreach ($options as $option)
				{
					
					// The only way to identify a custom option without
					// hardcoding ID's is the label :-(
					// But manipulating options this way is hackish anyway
					if (isset($option['code']) && $option['code']=='vendor')
					{
						$addOption=false;
					}
				}
				if($addOption)
				{
					$value=$vendor->getPublicName();
					$customOptions[]= 
											array(
											'label'       => Mage::helper('csorder')->__('Vendor'),
											'value'       =>$value,
											'print_value' => $value,
											'formatted_option' => $value,
											'code' =>'vendor'
										);
									
					
					 $item->addOption(array(
						'code' => 'additional_options',
						'value' => serialize($customOptions),
					));
				}
			} 
		}
	}
	
	/**
     * Converting Custom Option Quote To Order
     *
     */
	public function setQuoteToOrder(Varien_Event_Observer $observer)
	{
		if(Mage::helper('csorder')->isActive()){
			$quoteItem = $observer->getItem();
			if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
				$orderItem = $observer->getOrderItem();
				$options = $orderItem->getProductOptions();
				$options['additional_options'] = unserialize($additionalOptions->getValue());
				$orderItem->setProductOptions($options);
			}
		}
	}
	
	/**
     * Setting Customer Address Attribute "for_vendor" On Address Save
     *
     */
	public function setForVendor(Varien_Event_Observer $observer)
	{
		if(Mage::helper('csorder')->isActive()){
			if(is_null($observer->getCustomerAddress()->getForVendor())||$observer->getCustomerAddress()->getForVendor()=='')
			{
				$observer->getCustomerAddress()->setForVendor(0);
			}
		}
	}
		
	/**
	 *Change Order State on invoice
	 *
	 */
	public function changeOrderPaymentState($observer) {
		if(Mage::helper('csorder')->isActive()){
			$invoice = $observer->getDataObject();
			$order = $invoice->getOrder();
			Mage::helper('csmarketplace')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_PAYMENT_STATE_CHANGED);
			/* if ($order->getBaseTotalDue() == 0) */ {
				$vorders = Mage::getModel('csmarketplace/vorders')
								->getCollection()
								->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
				
								
				if (count($vorders) > 0) {
					foreach ($vorders as $vorder) {
						try{	
								$qtyOrdered=0;
								$qtyInvoiced=0;
							
								$vendorId=$vorder->getData('vendor_id');
								$vorderItems=Mage::getModel('sales/order_item')->getCollection()->addFieldToSelect('*')
								->addFieldToFilter('vendor_id',$vendorId)
								->addFieldToFilter('order_id',$order->getId());
							
								
								foreach($vorderItems as $item)
								{
										
										$qtyOrdered+=(int)$item->getQtyOrdered();
									
									
										$qtyInvoiced+=(int)$item->getData('qty_invoiced');
										
								}
							
								if($qtyOrdered>$qtyInvoiced)
									$vorder->setOrderPaymentState(Ced_CsOrder_Model_Sales_Order_Invoice::STATE_PARTIALLY_PAID);
								else
									$vorder->setOrderPaymentState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
							
								$vorder->save();
								Mage::helper('csmarketplace')->logProcessedData($vorder->getData(), Ced_CsMarketplace_Helper_Data::VORDER_PAYMENT_STATE_CHANGED);

							}
							catch(Exception $e){
								Mage::helper('csmarketplace')->logException($e);echo $e->getMessage();die;
							}
					}
					
				}					 
			}
			return $this;
		}
		else
		{
			parent::changeOrderPaymentState($observer);
		}
		//$invocies = $order->getInvoiceCollection(); 
	}
	
}