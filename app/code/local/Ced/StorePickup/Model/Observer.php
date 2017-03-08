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
* @package     Ced_StorePickup
* @author      CedCommerce Core Team <connect@cedcommerce.com >
* @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/ 
Class Ced_StorePickup_Model_Observer
{
	/**
	 * Predispath admin action controller
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function preDispatch(Varien_Event_Observer $observer)
	{
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			$feedModel  = Mage::getModel('storepickup/feed');
			/* @var $feedModel Ced_Core_Model_Feed */
			$feedModel->checkUpdate();
	
		}
	}
	
		public function beforeLoadingLayout(Varien_Event_Observer $observer) {

		try {
			
			$action = $observer->getEvent()->getAction();
			$layout = $observer->getEvent()->getLayout();
			$sec = $action->getRequest()->getParam('section');
			
			if($action->getRequest()->getActionName() == 'cedpop') return $this;
			$modules = Mage::helper('storepickup')->getCedCommerceExtensions();
			if(preg_match('/ced/i',strtolower($action->getRequest()->getControllerModule())) || $sec=="carriers"){

				foreach ($modules as $moduleName=>$releaseVersion)
				{ 	

					$m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }  $h = Mage::getStoreConfig(Ced_StorePickup_Block_Extensions::HASH_PATH_PREFIX.$m.'_hash'); for($i=1;$i<=(int)Mage::getStoreConfig(Ced_StorePickup_Block_Extensions::HASH_PATH_PREFIX.$m.'_level');$i++){$h = base64_decode($h);}$h = json_decode($h,true); 
					if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && $h['module_name'] == $m && $h['license'] == Mage::getStoreConfig(Ced_StorePickup_Block_Extensions::HASH_PATH_PREFIX.$m)){}else{ $_POST=$_GET=array();$action->getRequest()->setParams(array());$exist = false; foreach($layout->getUpdate()->getHandles() as $handle){ if($handle=='c_e_d_c_o_m_m_e_r_c_e'){ $exist = true; break; } } if(!$exist){ $layout->getUpdate()->addHandle('c_e_d_c_o_m_m_e_r_c_e'); }}	
				}
			}
			return $this;
			
		} catch (Exception $e) {
			return $this;
		}
	}
	
	
	public function paymentMethodIsActive(Varien_Event_Observer $observer) {
		
		
		$event           = $observer->getEvent();
		$method          = $event->getMethodInstance();
		$result          = $event->getResult();
		$currencyCode    = Mage::app()->getStore()->getCurrentCurrencyCode();
		
		
		$configData = Mage::getStoreConfig('carriers/ced_storepickup/activepaymethods');
		//print_r($configData);die("kj");
		$paymethods = explode(',', $configData);
		//print_r($paymethods[0]->getCode());die("kj");
	$shippping_method =	Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
	
		
		
		if (strpos($shippping_method, 'vendor_rates_ced_storepickup') !== false OR strpos($shippping_method, 'ced_storepickup') != false) 
			{
			
				if(in_array($method->getCode(), $paymethods))
				{
					$result->isAvailable = true;
				}else{
					$result->isAvailable = false;
				}
			}
		
	
	}
	
}
