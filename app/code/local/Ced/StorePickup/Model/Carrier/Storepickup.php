<?php
class Ced_StorePickup_Model_Carrier_Storepickup
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
	protected $_carriercode = "ced_storepickup";
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		$configData = Mage::getStoreConfig('carriers/ced_storepickup/active');
	
	 if ($configData != 1)
		 {
			return false;
		 }  
		 $result = Mage::getModel('shipping/rate_result');
		 $flag = true;
		 $countryIds = array();
		 $regionCodes = array();
		 
		 /*
		  * Get all store pickup locations
		 */
		 
		 //print_r($this->getConfigData('blockdays'));die("ljlk");
		 $storesCollection = Mage::getModel('storepickup/storepickup')->getCollection()
		 ->addFieldToFilter('is_active','1')->addFieldToFilter('vendor_id',0);
		// print_r($storesCollection->getData());die("kn");
		 $destcountry = $request->getDestCountryId();
		 $destRegion =  $request->getDestRegionCode();
		 $destStreet = $request->getDestStreet();
		 $destcity = $request->getDestCity();
		 $destpostcode = $request->getDestPostcode();
		 
		 $count = 0;
		$i = 1;
		 if ($flag) {
		 	foreach($storesCollection as $stores) {
		 		$i++;
		 		if($stores['store_country'] == $destcountry )
		 		{
		 			
		 			$countryIds[] = $stores->getStoreCountry();
		 			$regionCodes[] = $stores->getStoreState();
		 			$address[] = $stores['store_address'].', '.$stores['store_city'].', '.$stores['store_state'].', '.$stores['store_country'];
		 			//print_r($address[$count]); die("lkfgj");
		 			$method = $rate = Mage::getModel('shipping/rate_result_method');
		 			$method->setCarrier($this->_carriercode);
		 			$method->setCarrierTitle($address[$count]);
		 			$method->setMethod($i.$this->_carriercode);
		 			$method->setMethodTitle($address[$count]);
		 			$method->setPrice($stores['shipping_price']);
		 			$method->setCost($stores['shipping_price']);
		 			$result->append($method);
		 			$count++;
		 		}
		 	}
		 }
		 return $result;
		 
	}
    public function getAllowedMethods()
    {
    	return array(
    			$this->_carriercode => $this->getConfigData('name'),
    	);
     }
}