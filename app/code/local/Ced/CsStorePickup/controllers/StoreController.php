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
 * @package     Ced_CsStorePickup
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsStorePickup_StoreController extends Ced_CsMarketplace_Controller_AbstractController
{
	public function indexAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		if(!Mage::helper('csmultishipping')->isEnabled()){
			$this->_redirect('csmarketplace/vendor/index');
			return;
		}
		
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('StorePickup')." ".$this->__('StorePickup'));
		$this->renderLayout();
	}
	
	public function editAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Stores Info')." ".$this->__('Stores Info'));
		$this->renderLayout();
	}
	
	public function newAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		$this->_forward('edit');
		//$this->getLayout()->getBlock('head')->setTitle($this->__('Create new Store')." ".$this->__('Create new Store'));
		
	}
	public function saveAction()
	{
		
		if(!$this->_getSession()->getVendorId())
			return;
        $data = $this->getRequest()->getPost();
        
    
		$store_name = $data['vstore']['store_name'];
        $store_manager_name = $data['vstore']['store_manager_name'];
        $store_manager_email = $data['vstore']['store_manager_email'];
        $store_address = $data['vstore']['store_address'];
        $store_city = $data['vstore']['store_city'];
        $store_country = $data['vstore']['store_country'];
        $store_state = $data['vstore']['store_state'];
        $store_zcode = $data['vstore']['store_zcode'];
        $store_phone = $data['vstore']['store_phone'];
        $shipping_price = $data['vstore']['shipping_price'];
    
        
    	$address = $store_address.' '.$store_city.' '.$store_state.' '.$store_country;
		$address = str_replace(" ", "+", $address);
		$address_url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $address_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response);
	//	$latitude = '';$longitude='';
        if(isset($response_a->results[0])){
		$latitude = $response_a->results[0]->geometry->location->lat;
		$longitude = $response_a->results[0]->geometry->location->lng;
    }
        
   if((!isset($latitude)) || (!isset($longitude)) )
   {
      
    	Mage::getSingleton('core/session')->addError("Please provide correct address that can be located at google map ");
    	$this->_redirect('csstorepickup/store/index');
    	return;
   }
     
        $is_active = $data['vstore']['is_active'];
		
		 $storeHourInfo = array('Monday' => array('status' => $data['days_status']['mon'], 'start' => $data['start']['mon'], 'end' =>$data['end']['mon'], 'interval' => $data['interval']['mon']),
                               'Tuesday'=> array('status' => $data['days_status']['tue'], 'start' => $data['start']['tue'], 'end' =>$data['end']['tue'], 'interval' => $data['interval']['tue']),
                               'Wednesday'=> array('status' => $data['days_status']['wed'], 'start' => $data['start']['wed'], 'end' =>$data['end']['wed'], 'interval' => $data['interval']['wed']),
                               'Thursday'=> array('status' => $data['days_status']['thu'], 'start' => $data['start']['thu'], 'end' =>$data['end']['thu'], 'interval' => $data['interval']['thu']),
                               'Friday'=> array('status' => $data['days_status']['fri'], 'start' => $data['start']['fri'], 'end' =>$data['end']['fri'], 'interval' => $data['interval']['fri']),
                               'Saturday'=> array('status' => $data['days_status']['sat'], 'start' => $data['start']['sat'], 'end' =>$data['end']['sat'], 'interval' => $data['interval']['sat']),
                               'Sunday'=> array('status' => $data['days_status']['sun'], 'start' => $data['start']['sun'], 'end' =>$data['end']['sun'], 'interval' => $data['interval']['sun'])
                               ); 
	
		 $hourInfo = json_encode($storeHourInfo);
	
        if ($data) {
            $model = Mage::getModel('storepickup/storepickup');
            $id = $this->getRequest()->getParam('pickup_id');
           
			try {
			     if ($id) {
    				$model->load($id);
    				$model->setData('store_name',$store_name);
    				$model->setData('store_manager_name',$store_manager_name);
    				$model->setData('store_manager_email',$store_manager_email);
    				$model->setData('store_address',$store_address);
    				$model->setData('store_city',$store_city);
    				$model->setData('store_country',$store_country);
    				$model->setData('store_state',$store_state);
    				$model->setData('store_zcode',$store_zcode);
    				$model->setData('latitude',$latitude);
    				$model->setData('longitude',$longitude);
    				$model->setData('store_phone',$store_phone);
    				$model->setData('is_active',$is_active);
    				$model->setData('shipping_price',$shipping_price);
    				$model->setData('days',$hourInfo);
    				
    				$model->save();
    				
    				
    			} else {
    			
    				$vendor_id = $this->_getSession()->getVendorId();
    				$model = Mage::getModel('storepickup/storepickup');
    				
    				$model->setData('store_name',$store_name);
    				$model->setData('store_manager_name',$store_manager_name);
    				$model->setData('store_manager_email',$store_manager_email);
    				$model->setData('store_address',$store_address);
    				$model->setData('store_city',$store_city);
    				$model->setData('store_country',$store_country);
    				$model->setData('store_state',$store_state);
    				$model->setData('store_zcode',$store_zcode);
					$model->setData('latitude',$latitude);
    				$model->setData('longitude',$longitude);
    				$model->setData('store_phone',$store_phone);
    				$model->setData('is_active',$is_active);
    				$model->setData('shipping_price',$shipping_price);
    				$model->setData('days',$hourInfo);
    				$model->setData('vendor_id',$vendor_id);
                    $model->setData('is_approved',0);
    			
    				$model->save(); 
    				
    				
    				
    			}
                Mage::getSingleton('adminhtml/session')->addSuccess(__('The store pickup information has been saved.'));
             
               
                return $this->_redirect('csstorepickup/store/index');
            } catch (Exception $e){
            	die($e);
            	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
		
        }
       
     
    }
    public function deleteAction()
    {
    	$pickup_id = $this->getRequest()->getParam('pickup_id');
    	$pickup_ids = explode(",",$pickup_id);
    	
    	foreach ($pickup_ids as $pickup_id)
    	{
    		 
    		try {
    			Mage::getModel('storepickup/storepickup')->load($pickup_id)->delete();
    
    
    		} catch (Exception $e) {
    			$this->messageManager->addError($e->getMessage());
    		}
    
    	}
    	 
    	return $this->_redirect('*/*/');
    	Mage::getSingleton('adminhtml/session')->addSuccess(__('The store has been successfully Deleted.'));
    	 
    	 
    }
   
     public function gridAction()
    {
    	if(!$this->_getSession()->getVendorId())
    		return;
    
    	$this->loadLayout();
    	$this->renderLayout();
    } 
    
    
    public function enableAction()
    {
    	$pickup_id = $this->getRequest()->getParam('pickup_id');
    	$pickup_ids = explode(",",$pickup_id);
    	
    	foreach ($pickup_ids as $pickup_id)
    	{
    		 
    		try {
    			Mage::getModel('storepickup/storepickup')->load($pickup_id)->setIsActive(1)->save();
    
    
    		} catch (Exception $e) {
    			$this->messageManager->addError($e->getMessage());
    		}
    
    	}
    	 
    	return $this->_redirect('*/*/');
    	Mage::getSingleton('adminhtml/session')->addSuccess(__('The store has been successfully Enabled.'));
    	 
    	 
    }
    public function disableAction()
    {
    	$pickup_id = $this->getRequest()->getParam('pickup_id');
    	$pickup_ids = explode(",",$pickup_id);
    
    	 
    	foreach ($pickup_ids as $pickup_id)
    	{
    		 
    		try {
    			Mage::getModel('storepickup/storepickup')->load($pickup_id)->setIsActive(0)->save();
    
    
    		} catch (Exception $e) {
    			$this->messageManager->addError($e->getMessage());
    		}
    
    	}
    
    	return $this->_redirect('*/*/');
    	Mage::getSingleton('adminhtml/session')->addSuccess(__('The store has been successfully Disabled.'));
    
    
    }
    
}