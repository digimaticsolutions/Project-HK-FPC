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
require_once ('Ced'.DS.'CsMarketplace'.DS.'controllers'.DS.'Adminhtml'.DS.'VpaymentsController.php');
class Ced_CsTransaction_Adminhtml_VpaymentsController extends Ced_CsMarketplace_Adminhtml_VpaymentsController
{
	
 	/**
	 * Save Payment Information
	 *
	 */
	public function saveAction() {
		/* if(!Mage::helper('csorder')->isActive())
		{
			return parent::saveAction();
		} */
		if ($data = $this->getRequest()->getPost()) {
			$params = $this->getRequest()->getParams();
			$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		
			$model = Mage::getModel('csmarketplace/vpayment');
			$amount_desc = isset($data['amount_desc'])?$data['amount_desc']:json_encode(array());
			$shipping_info = isset($data['shipping_info'])?$data['shipping_info']:json_encode(array());
			$shipping_info = json_decode($shipping_info,true);
			$total_shipping_amount = isset($data['total_shipping_amount'])?$data['total_shipping_amount']:0;
			$total_amount = json_decode($amount_desc,true);
			Mage::helper('csmarketplace')->logProcessedData($total_amount, Ced_CsMarketplace_Helper_Data::VPAYMENT_TOTAL_AMOUNT);
			
			$baseCurrencyCode = Mage::app()->getBaseCurrencyCode();
			$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
			$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
			$data['base_to_global_rate'] = isset($data['currency'])&& isset($rates[$data['currency']]) ? $rates[$data['currency']] : 1;
			$oldAmountDesc=array();
			$base_amount = 0;
			if(count($total_amount) > 0) {
				foreach($total_amount as $orderid=>$items) {
					foreach($items as $vorderItemId=>$value){
						$vorder = Mage::getModel('sales/order')->load($orderid);
						$incrementId=$vorder->getIncrementId();
						if(isset($oldAmountDesc[$incrementId]))
							$oldAmountDesc[$incrementId]+=$value;
						else
							$oldAmountDesc[$incrementId]=$value;
						$base_amount += (float)$value;
					}
				}
			}
			$oldAmountDesc=json_encode($oldAmountDesc);
			$data['item_wise_amount_desc']=$data['amount_desc'];
			$data['amount_desc']=$oldAmountDesc;
			$base_amount+=$total_shipping_amount;
			if($base_amount != $data['base_amount']) {
				Mage::getSingleton('adminhtml/session')->addError('Amount entered should be equal to the sum of all selected order(s)');
				$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
                return;
			}
		
			$data['transaction_type'] = $type;
			$data['payment_method'] = isset($data['payment_method'])?$data['payment_method']:0;
			/*Will use it when vendor will pay in different currenncy  */
			
			list($currentBalance,$currentBaseBalance) = $model->getCurrentBalance($data['vendor_id']);
			$base_net_amount = $data['base_amount']+$data['base_fee'];				
			if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
				/* Case of Deduct credit */
				if($currentBaseBalance > 0) $newBaseBalance = $currentBaseBalance - $base_net_amount;
				else $newBaseBalance = $base_net_amount;
				$base_net_amount = -$base_net_amount;
				if(-$base_net_amount <= 0.00) {
					Mage::getSingleton('adminhtml/session')->addError("Refund Net Amount can't be less than zero");
					$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
					return;
				}
			} else {
				// Case of Add credit 
				$newBaseBalance = $currentBaseBalance + $base_net_amount;
				if($base_net_amount <= 0.00) {
					Mage::getSingleton('adminhtml/session')->addError("Net Amount can't be less than zero");
					$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
					return;
				}
			}
			
			
			
			$data['base_currency']= $baseCurrencyCode;
			$data['base_net_amount'] = $base_net_amount;
			$data['base_balance'] = $newBaseBalance;
			
			$data['amount'] = $base_amount*$data['base_to_global_rate'];
			$data['balance'] = Mage::helper('directory')->currencyConvert($newBaseBalance, $baseCurrencyCode, $data['currency']);
			$data['fee'] = Mage::helper('directory')->currencyConvert($data['base_fee'], $baseCurrencyCode, $data['currency']);
			$data['net_amount'] = Mage::helper('directory')->currencyConvert($base_net_amount, $baseCurrencyCode, $data['currency']);
			
			$data['tax'] = 0.00;
			$data['payment_detail'] = isset($data['payment_detail'])?$data['payment_detail']:'n/a';
			
			$model->addData($data);
			$openStatus = $model->getOpenStatus();
			$model->setStatus($openStatus);
			
			try {
				$model->saveOrders($data);
				$model->save();
				foreach($shipping_info as $vorderId=>$shippingAmount)
				{
					$vorder=Mage::getModel('csmarketplace/vorders')->load($vorderId);
					if($vorder->getId()){
						if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
							$vorder->setShippingRefunded((float)$vorder->getShippingRefunded()+$shippingAmount);
						}
						else
						{
							$vorder->setShippingPaid((float)$vorder->getShippingPaid()+$shippingAmount);
						}
						$vorder->save();
					}
				}
				
				Mage::helper('csmarketplace')->logProcessedData($model->getData(), Ced_CsMarketplace_Helper_Data::VPAYMENT_CREATE);

				Mage::app()->cleanCache();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__('Payment is  successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
				Mage::helper('csmarketplace')->logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__('Unable to find vendor to save'));
        $this->_redirect('*/*/');
	}
	
}