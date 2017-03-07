<?php 

 /**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsCommission
  * @author  	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Vendor Miscellaneous rate model
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author 		CedCommerce Core Team <connect@cedcommerce.com >
 */
class Ced_CsCommission_Model_Vendor_Rate_Miscellaneous extends Ced_CsMarketplace_Model_Vendor_Rate_Abstract
{
	protected $_base_fee = 0;
	protected $_fee = 0;
	/**
	 * Retrive miscellaneous conditions from settings 
	 * (vendor specific OR vendor group specific OR global)
	 *
	 * @param int
	 * @return array
	 */
	protected function _getMiscellaneousConditions($vendorId,$storeId) {
		if(Mage::registry('current_order_vendor')) 
			Mage::unregister('current_order_vendor');
		
		Mage::register('current_order_vendor',$vendorId);
		
		$productTypes = Mage::helper('cscommission/product')->getUnserializedOptions();
		$categoryWise = Mage::helper('cscommission/category')->getUnserializedOptions();				
		/*Customize code to get sales, ship, payments & service tax */		
		$salesCalMethod = Mage::getStoreConfig('ced_vpayments/general/commission_mode_sales',$storeId);		
		$salesRate = Mage::getStoreConfig('ced_vpayments/general/commission_fee_sales',$storeId);		
		$shipCalMethod = Mage::getStoreConfig('ced_vpayments/general/commission_mode_ship',$storeId);		
		$shipRate = Mage::getStoreConfig('ced_vpayments/general/commission_fee_ship',$storeId);		
		$paymentCalMethod = Mage::getStoreConfig('ced_vpayments/general/commission_mode_payments',$storeId);		
		$paymentRate = Mage::getStoreConfig('ced_vpayments/general/commission_fee_paymnets',$storeId);		
		$servicetaxCalMethod = Mage::getStoreConfig('ced_vpayments/general/commission_mode_servicetax',$storeId);		
		$servicetaxRate = Mage::getStoreConfig('ced_vpayments/general/commission_fee_servicetax',$storeId);		
		/*End*/		
		/* Mage::log('Vendor Id:{{'.$vendorId.'}}[['.Mage::registry('current_order_vendor').']]'.print_r($productTypes,true).print_r($categoryWise,true),null,'condition_log.log'); */
		Mage::unregister('current_order_vendor');
		return array($productTypes,$categoryWise,$salesCalMethod,$salesRate,$shipCalMethod,$shipRate,$paymentCalMethod,$paymentRate,$servicetaxCalMethod,$servicetaxRate);
	}
	
	/**
	 * Get the commission based on group
	 *
	 * @param float $grand_total
	 * @param float $base_grand_total
	 * @param string $currency
	 * @param array $commissionSetting
	 * @return array
	 */
	public function calculateCommission($grand_total = 0, $base_grand_total = 0, $base_to_global_rate = 1, $commissionSetting = array()) {
		try {
			$result = array();			
			$order = $this->getOrder();			
			$vendorId = $this->getVendorId();			
			$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);			
			$result['base_fee'] = 0;			
			$result['fee'] = 0;			
			$salesCost = 0;			
			$shipCost = 0;			
			$paymentCost = 0;			
			$serviceTaxCost = 0;			
			/*list($productTypes,$categoryWise) = $this->_getMiscellaneousConditions($vendorId);*/			
			list($productTypes,$categoryWise,$salesCalMethod,$salesRate,$shipCalMethod,$shipRate,$paymentCalMethod,$paymentRate,$servicetaxCalMethod,$servicetaxRate) = $this->_getMiscellaneousConditions($vendorId,$order->getStoreId());			
			$itemCommission = isset($commissionSetting['item_commission']) ? $commissionSetting['item_commission'] : array();			
			$customTotalPrice = 0;			
			foreach($itemCommission as $key => $itemPrice) {				
				$customTotalPrice = $customTotalPrice + $itemPrice;			
			}
			/*
			echo "{{";
			echo $salesRate.'==';
			echo $salesCalMethod;
			echo "}}{{";
			echo $shipRate.'==';
			echo $shipCalMethod;
			echo "}}{{";
			echo $paymentRate.'==';
			echo $paymentCalMethod;
			echo '}}';
			die;
			*/
			$salesCost = Mage::helper('cscommission')->calculateFee($customTotalPrice,$salesRate,$salesCalMethod);			
			$shipCost = Mage::helper('cscommission')->calculateFee($customTotalPrice,$shipRate,$shipCalMethod);			
			$paymentCost = Mage::helper('cscommission')->calculateFee($customTotalPrice,$paymentRate,$paymentCalMethod);			
			/*$serviceTaxCost = Mage::helper('cscommission')->calculateFee($customTotalPrice,$servicetaxRate,$servicetaxCalMethod);*/			
			$custom_base_fee = $salesCost + $shipCost + $paymentCost;			
			$custom_fee = Mage::helper('directory')->currencyConvert($custom_base_fee, $order->getBaseCurrencyCode(), 
			$order->getGlobalCurrencyCode());			
			$result['base_fee'] = $custom_base_fee;			
			$result['fee'] = $custom_fee;			
			/* print_r($result);die; */
			if(count($productTypes) > 0 || count($categoryWise) > 0) {				
				$item_commission = array();				
				foreach($order->getAllItems() as $item) {					
					if($item->getVendorId() && $item->getVendorId() == $vendorId) {						
						$temp_base_fee = 0;						
						$temp_fee = 0;						
						$product_temp_priority = array();						
						$category_temp_priority = array();						
						$product = Mage::getModel('catalog/product')->load($item->getProductId());						
						$productTypeId = (string)$product->getTypeId();						
						if(is_array($product->getCategoryIds())) {							
							$productCategoriesIds = (array)$product->getCategoryIds();						
						} else {							
							$productCategoriesIds = explode(',',trim((string)$product->getCategoryIds()));						
						}						
						$productCategoriesIds = (array)$productCategoriesIds;						
						$assignedProductType = array_keys($productTypes);						
						$assignedCategory = array_keys($categoryWise);						
						if (isset($productTypes[$productTypeId])) {							
							$product_temp_priority = $productTypes[$productTypeId];						
						} elseif(in_array('alltype',$assignedProductType)) {							
							$product_temp_priority = $productTypes['alltype'];						
						}														
						foreach($categoryWise as $id=>$condition) {	
							$categoryId = isset($condition['category']) && (int)$condition['category']?(int)$condition['category']:0;
							if (!$categoryId) continue;
							if(in_array($categoryId,$productCategoriesIds)) {								
								if(!isset($category_temp_priority['priority']) || (isset($category_temp_priority['priority']) && (int)$category_temp_priority['priority'] > (int)$condition['priority'])) {									
									$category_temp_priority = $condition;								
								}							
							}						
						} 
						/*					
						foreach($productCategoriesIds as $category) {							
							if (isset($categoryWise[$category])) {								
								if(!isset($category_temp_priority['priority']) || (isset($category_temp_priority['priority']) && (int)$category_temp_priority['priority'] > (int)$categoryWise[$categoryId]['priority'])) {									
									$category_temp_priority = $categoryWise[$categoryId];								
								}							
							}						
						}
						*/
						/* if($_SERVER['REMOTE_ADDR'] == '182.74.41.196') {
							print_r($categoryWise);
							echo '==';
							print_r($productCategoriesIds);
							echo '==';
							print_r($category_temp_priority);die;
						} */
						if(!isset($category_temp_priority['priority']) && isset($categoryWise['all'])) {							
							$category_temp_priority = $categoryWise['all'];						
						}						
						/* Calculation starts for fee calculation */						
						/* START */						
							$log = array();						
							$pt = isset($product_temp_priority['fee'])?$product_temp_priority['fee']:Mage::getStoreConfig('ced_vpayments/general/commission_fee_default',$order->getStoreId());						
							$cw = isset($category_temp_priority['fee'])?$category_temp_priority['fee']:Mage::getStoreConfig('ced_vpayments/general/commission_fee_default',$order->getStoreId());						
							$log[$order->getId()][$vendorId]['group'] = Mage::getModel('csmarketplace/vendor')->load($vendorId)->getGroup();						
							$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['pt']['rate'] = $pt;						
							$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cw']['rate'] = $cw;						
							$pt = isset($product_temp_priority['method'])?Mage::helper('cscommission')->calculateFee($itemCommission[$item->getId()],$pt,$product_temp_priority['method']):Mage::helper('cscommission')->calculateFee($itemCommission[$item->getId()],$pt,Mage::getStoreConfig('ced_vpayments/general/commission_mode_default',$order->getStoreId()));						
							$cw = isset($category_temp_priority['method'])?Mage::helper('cscommission')->calculateFee($itemCommission[$item->getId()],$cw,$category_temp_priority['method']):Mage::helper('cscommission')->calculateFee($itemCommission[$item->getId()],$cw,Mage::getStoreConfig('ced_vpayments/general/commission_mode_default',$order->getStoreId()));						
							$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['pt']['fee'] = $pt;						
							$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cw']['fee'] = $cw;						
							$cf = Mage::getStoreConfig('ced_vpayments/general/commission_fn',$order->getStoreId());						
							$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cf']['method']['name'] = $cf;						
							switch($cf) {							
								case Ced_CsCommission_Model_Source_Vendor_Rate_Aggregrade::TYPE_MIN :							
									$temp_base_fee = min($pt,$cw);							
									$temp_fee = Mage::helper('directory')->currencyConvert($temp_base_fee, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
									$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cf']['method']['min']['base_fee'] = $temp_base_fee;
									$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cf']['method']['min']['fee'] = $temp_fee;
									break;
								case Ced_CsCommission_Model_Source_Vendor_Rate_Aggregrade::TYPE_MAX :
								default :
									$temp_base_fee = max($pt,$cw);
									$temp_fee = Mage::helper('directory')->currencyConvert($temp_base_fee, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
									$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cf']['method']['max']['base_fee'] = $temp_base_fee;
									$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['cf']['method']['max']['fee'] = $temp_fee;
									break;
							}
						/* END */
						$result['base_fee'] = $result['base_fee'] + $temp_base_fee;
						$result['fee'] = $result['fee'] + $temp_fee;
						$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['result'] = $result;
						$item_commission[$item->getId()] = array('base_fee' => $temp_base_fee, 'fee' => $temp_fee);
						$log[$order->getId()][$vendorId][$item->getId()][$item->getProductId()]['item_commission'] = $item_commission[$item->getId()];
						/* Mage::log(print_r($log,true),null,'cscommission_calculation.log',true); */
					}
				}
				
				$totalBaseFeeCommisionExludeServiceTax = 0;
				$totalBaseFeeCommisionIncludeServiceTax =0;
				$finalCommision = 0;
				$totalBaseFeeCommisionExludeServiceTax = $result['base_fee'];
				$serviceTaxCost = Mage::helper('cscommission')->calculateFee($totalBaseFeeCommisionExludeServiceTax,$servicetaxRate,$servicetaxCalMethod);
				$totalBaseFeeCommisionIncludeServiceTax = $totalBaseFeeCommisionExludeServiceTax + $serviceTaxCost;
				/* echo $totalBaseFeeCommisionIncludeServiceTax.'==';
				echo $customTotalPrice;
				die;
				*/
				$finalCommision = min($totalBaseFeeCommisionIncludeServiceTax,$customTotalPrice);
				$result['base_fee'] = $finalCommision;
				$result['fee'] = Mage::helper('directory')->currencyConvert($finalCommision, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
				$result['item_commission'] = json_encode($item_commission);
				/* print_r($result);die; */
				/* Mage::log(print_r($result,true),null,'cscommission_calculation.log',true); */
			}
			return $result;
		} catch (Exception $e) {
			Mage::log($e->getMessage(),null,'cscommission_exceptions.log');
		}
	}
	
}
