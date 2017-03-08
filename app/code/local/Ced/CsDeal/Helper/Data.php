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
 * @category    Ced
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsDeal_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	* is admin approval needed for deal 
	*@return bool
	**/
	protected $_statuses; 

	public function initDeal($product)
	{
		$this->_statuses=$product->getId();	
	}
	
	public function isApprovalNeeded()
	{
		$store_id = Mage::app()->getStore()->getStoreId();
		$approval=Mage::getStoreConfig('ced_csmarketplace/csdeal/csdeal_approval', $store_id);
		if($approval)
			return true;
		else
			return false;
	}
	public function getDealText()
	{
		$store_id = Mage::app()->getStore()->getStoreId();
		if(Mage::registry('current_vendor')){
		$vendor_id=Mage::registry('current_vendor')->getId();	}else{
			if(Mage::registry('product'))
			$product=Mage::registry('product')->getId();
			if(!$product)
			$product=$this->_statuses;	
			$vendor_id=Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($product);
			
		}
		$setting=Mage::getModel('csdeal/dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
		if(count($setting->getData())){
			$dealtext=$setting->getDealMessage();
		}else{
		$dealtext=Mage::getStoreConfig('ced_csmarketplace/csdeal/csdeal_default_text', $store_id);
		}
		if($dealtext)
			return $dealtext;
	}

	public function ShowTimer()
	{
		$product = '';
		if(!$this->isModuleEnable()) return;
		$store_id = Mage::app()->getStore()->getStoreId();
		if(Mage::registry('current_vendor')){
		$vendor_id=Mage::registry('current_vendor')->getId();	}else{
			if(Mage::registry('product'))
			$product=Mage::registry('product')->getId();
			if(!$product)
			$product=$this->_statuses;	
			$vendor_id=Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($product);
			
		}		
		$setting=Mage::getModel('csdeal/dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
		
		$ShowTimer='';
		if(count($setting->getData())>0){
			$ShowTimer=$setting->getTimerList();
		}else{
			$ShowTimer==Mage::getStoreConfig('ced_csmarketplace/csdeal/csdeal_timer', $store_id);
		}
		
		switch ($ShowTimer) {
					case 'list':
						 if(Mage::app()->getFrontController()->getAction()->getFullActionName()=='csmarketplace_vshops_view'){
						 	return true;
						 }
						 return false;
						break;
					case 'view':
						 if(Mage::app()->getFrontController()->getAction()->getFullActionName()=='catalog_product_view'){
						 	return true;
						 }
						 return false;
						break;
					
					default:
						return true;
						break;
				}
	}
	public function ShowDeal()
	{
		$product = '';
		if(!$this->isModuleEnable()) return;

		$store_id = Mage::app()->getStore()->getStoreId();
		if(Mage::registry('current_vendor')){
		$vendor_id=Mage::registry('current_vendor')->getId();	}else{
			if(Mage::registry('product'))
			$product=Mage::registry('product')->getId();
			if(!$product)
			$product=$this->_statuses;	
			$vendor_id=Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($product);
			
		}	
		$setting=Mage::getModel('csdeal/dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
		if(count($setting->getData())){
			$ShowDeal=$setting->getDealList();
		}else{
		$ShowDeal=Mage::getStoreConfig('ced_csmarketplace/csdeal/csdeal_show', $store_id);
		}return $ShowDeal;
	}
	public function getDealEnd($product_id)
	{
		if(!$this->isModuleEnable()) return;
		$deal=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('status',Ced_CsDeal_Model_Status::STATUS_ENABLED)->addFieldToFilter('product_id',$product_id)->getFirstItem();
		return $deal->getEndDate();
	}
	public function isModuleEnable()
	{	
		$product = '';
		$store_id = Mage::app()->getStore()->getStoreId();
		if(Mage::registry('current_vendor')){
		$vendor_id=Mage::registry('current_vendor')->getId();	}else{
			if(Mage::registry('product'))
			$product=Mage::registry('product')->getId();
			if(!$product)
			$product=$this->_statuses;	
			$vendor_id=Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($product);
			
		}
		$setting=Mage::getModel('csdeal/dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
		if(count($setting->getData())){
			$status=$setting->getStatus();
		}else{
				$status=Mage::getStoreConfig('ced_csmarketplace/csdeal/enable', $store_id);
		}
		if($status)
			return true;
		else
			return false;
	}
	public function getDealDay($deal)
	{
		$days=$deal->getDays();

		if($days){
			return true;
		}else{
			$specifiday=$deal->getSpecificdays();
			$specifiday=explode(',', $specifiday);
			switch (date("l")) {
				case 'Monday':
					if(in_array('mon',$specifiday))
					return true;	
					break;
				case 'Tuesday':
					if(in_array('tue',$specifiday))
					return true;
					break;
				case 'Wednesday':
					if(in_array('wed',$specifiday))
					return true;
					break;
				case 'Thursday':
					if(in_array('thu',$specifiday))
					return true;
					break;
				case 'Friday':
					if(in_array('fri',$specifiday))
					return true;
					break;
				case 'Saturday':
					if(in_array('sat',$specifiday))
					return true;
					break;					
				case 'Sunday':
					if(in_array('sun',$specifiday))
					return true;
					break;	
				default:
					return true;
					break;
			}
		}
	}
	public function canShowDeal($product_id)
	{
		$deal=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('admin_status',Ced_CsDeal_Model_Deal::STATUS_APPROVED)->addFieldToFilter('status',Ced_CsDeal_Model_Status::STATUS_ENABLED)->addFieldToFilter('product_id',$product_id)->getFirstItem();
		if(count($deal->getData())){
			$endDate=$deal->getEndDate();
			$startDate=$deal->getStartDate();
			$timezone = Mage::getStoreConfig('general/locale/timezone');
			$tz_object = new DateTimeZone($timezone);
	    	$datetime = new DateTime();
	  	  	$datetime->setTimezone($tz_object);
	        $currentDate=$datetime->format('Y-m-d h:i:s');
	        $ifDealDay=$this->getDealDay($deal);
//if(strtotime($currentDate) >= strtotime($startDate) &&  strtotime($currentDate)<= strtotime($endDate) && $ifDealDay){die('bb');
//print_r(strtotime($currentDate).'----'. $currentDate);
			if((strtotime($currentDate) >= strtotime($startDate) ||  strtotime($currentDate)<= strtotime($endDate)) && $ifDealDay){
				switch ($this->ShowDeal()) {
					case 'list':
						 if(Mage::app()->getFrontController()->getAction()->getFullActionName()=='csmarketplace_vshops_view'){
						 	return true;
						 }
						 return false;
						break;
					case 'view':
						 if(Mage::app()->getFrontController()->getAction()->getFullActionName()=='catalog_product_view'){
						 	return true;
						 }
						 return false;
						break;
					
					default:
						return true;
						break;
				}
			}else{
				return false;
			}	
		}else{
			return false;
		}
		
	}
	
}
