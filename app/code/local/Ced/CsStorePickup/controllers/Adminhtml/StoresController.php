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
class Ced_CsStorePickup_Adminhtml_StoresController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		
		$this->loadLayout ();
		$this->getLayout()->getBlock('head')->setTitle($this->__('StorePickup'));
		$this->renderLayout ();
	}
	public function newAction()
	{
	
		$this->_forward('edit');
	
	}
	
	public function editAction()
	{
		$pickup_id =$this->getRequest()->getParam('pickup_id');
	
		if($pickup_id!="")
		{
			$Model=Mage::getModel('storepickup/storepickup')->load($pickup_id);
			
		
			Mage::register('storepickup_data', $Model);
		}
		$this->loadLayout();
		$this->_addContent($this->getLayout()
				->createBlock('storepickup/adminhtml_store_edit'))
				->_addLeft($this->getLayout()
						->createBlock('storepickup/adminhtml_store_edit_tabs')
				);
		$this->renderLayout();
	}
	
	public function saveAction()
	{
        $data = $this->getRequest()->getPost();
        
   
		$store_name = $data['store_name'];
        $store_manager_name = $data['store_manager_name'];
        $store_manager_email = $data['store_manager_email'];
        $store_address = $data['store_address'];
        $store_city = $data['store_city'];
        $store_country = $data['store_country'];
        $store_state = $data['store_state'];
        $store_zcode = $data['store_zcode'];
        $store_phone = $data['store_phone'];
        $shipping_price = $data['shipping_price'];
     //   echo $shipping_price; die("fglkh");
        /*Calculate Tha Latitute and Longitude*/
        
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
		$latitude = $response_a->results[0]->geometry->location->lat;
		$longitude = $response_a->results[0]->geometry->location->lng;
		
		
		if((!isset($latitude)) || (!isset($longitude)) )
		{
		
			Mage::getSingleton('core/session')->addError("Please provide correct address that can be located at google map ");
			$this->_redirect('*/*/');
			return;
		}
       
        $is_active = $data['is_active'];
		
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
    				$model->setData('vendor_id',0);
    				
    				
    				$model->save(); 
    			
    			}
                Mage::getSingleton('adminhtml/session')->addSuccess(__('The store pickup information has been saved.'));
              Mage::getSingleton('adminhtml/session')
				->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $this->redirect('*/*/edit', ['pickup_id' => $model->getId(), '_current' => true]);
                }
                return $this->_redirect('*/*/');
            } catch (Exception $e){
				Mage::getSingleton('adminhtml/session')
				->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')
				->settestData($this->getRequest()
				->getPost()
				);
				$this->_redirect('*/*/edit',
						array('pickup_id' => $this->getRequest()
								->getParam('pickup_id')));
				return;
			}
		
        }
      
    }
    public function deleteAction()
    {
    	$pickup_id = $this->getRequest()->getParam('pickup_id');
    	
    	if(!is_array($pickup_id))
    	{
    		$pickup_ids[] = $pickup_id;
    	}
    	else{
    		$pickup_ids = $pickup_id;
    	}
    	
    	
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
    
    
    public function approvedAction(){
    	
    	if($this->getRequest()->getParam('pickup_id') && $this->getRequest()->getParam('pickup_id')>0){
    		$pickup_id = $this->getRequest()->getParam('pickup_id');
    		
    		
    		try{
    			$pickupStore = Mage::getModel('storepickup/storepickup')->load($pickup_id);
    			$pickupStore->setIsApproved('1');
    			$pickupStore->save();
    			$this->_getSession()->addSuccess(Mage::helper('catalog')->__('Pickup Store Approved Successfully'));
    			$this->_redirect('*/*/index');
    			return;
    		}catch(Exception $e)
    		{
    			$this->_getSession()->addError($e->getMessage());
    			$this->_redirect('*/*/index');
    			return;
    		}
    			
    	}
    	$this->_getSession()->addError(Mage::helper('catalog')->__('Failed TO Approve The Pickup Store'));
    	$this->_redirect('*/*/index');
    }
    /**
     *  Admin Dis approved Vendor stores
     */
    public function disapprovedAction(){
    	
    	if($this->getRequest()->getParam('pickup_id') && $this->getRequest()->getParam('pickup_id')>0){
    		$pickup_id = $this->getRequest()->getParam('pickup_id');
    		try{
    			$pickupStore = Mage::getModel('storepickup/storepickup')->load($pickup_id);
    			$pickupStore->setIsApproved('0');
    			$pickupStore->save();
    			$this->_getSession()->addSuccess(Mage::helper('catalog')->__('Pickup Store Disapproved Successfully'));
    			$this->_redirect('*/*/index');
    			return;
    		}catch(Exception $e)
    		{
    			$this->_getSession()->addError($e->getMessage());
    			$this->_redirect('*/*/index');
    			return;
    		}
    			
    	}
    	$this->_getSession()->addError(Mage::helper('catalog')->__('Fail TO Disapprove The Pickup Store'));
    	$this->_redirect('*/*/index');
    }
    
    
    public function massstatusAction()
    {
    	 $pickupIds = $this->getRequest()->getParam('pickup_id');
    	 $status = $this->getRequest()->getParam('status');
    	 
    	 
    	 foreach ($pickupIds as $id)
    	 {
    	 	try{
    	 		$pickupStore = Mage::getModel('storepickup/storepickup')->load($id);
    	 		if($status == 'Approve')
    	 		{
    	 			$pickupStore->setIsApproved('1');
    	 		}
    	 		else{
    	 			$pickupStore->setIsApproved('0');
    	 		}
    	 		$pickupStore->save();
    	 		
    	 	}
    	 	catch(Exception $e)
    	 	{
    	 		$this->_getSession()->addError($e->getMessage());
    	 		$this->_redirect('*/*/index');
    	 		return;
    	 	}
    	 }
    	 $this->_getSession()->addSuccess(Mage::helper('catalog')->__('Pickup Store Successfully'.$status.'d'));
    	 $this->_redirect('*/*/index');
    	
    }
}