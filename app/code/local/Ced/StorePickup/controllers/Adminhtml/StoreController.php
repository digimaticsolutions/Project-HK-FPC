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
class Ced_StorePickup_Adminhtml_StoreController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout ();
		$this->renderLayout ();
	}
	public function newAction()
	{
	
		$this->_forward('edit');
	
	}
	
	public function editAction()
	{
		$pickup_id =$this->getRequest()->getParam('pickup_id');
	//	echo $pickup_id; die("fkjgh");
		if($pickup_id!="")
		{
			$Model=Mage::getModel('storepickup/storepickup')->load($pickup_id);
			
		//print_r($Model->getData());die("jg");
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
        
    //   print_r($data);die("fjg");
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
       // print_r($response_a);
		if(isset($response_a->results[0])){
		$latitude = $response_a->results[0]->geometry->location->lat;
		$longitude = $response_a->results[0]->geometry->location->lng;
		}
		

		if((!isset($latitude)) || (!isset($longitude)) )
		{
		
			Mage::getSingleton('core/session')->addError("Please provide correct address that can be located at google map ");
			$this->_redirect('*/*/');
			return;
		}
		
		
        /*$latitude = $data['latitude'];
        $longitude = $data['longitude'];
		if($latitude && $longitude) {
			$latitude = $data['latitude'];
			$longitude = $data['longitude'];
		}else {
			//echo $store_address.' '.$store_city.' '.$store_state.' INDIA'.'<br>';
			$address = $store_address.' '.$store_city.' '.$store_state.' INDIA';
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
			echo $formatted_address = $response_a->results[0]->formatted_address.'<br />';
			echo $lat = $response_a->results[0]->geometry->location->lat.'<br />';
			echo $long = $response_a->results[0]->geometry->location->lng;
			die;
			$latitude = $response_a->results[0]->geometry->location->lat;
			$longitude = $response_a->results[0]->geometry->location->lng;
		}*/
        $is_active = $data['is_active'];
		//print_r($data); die("lkhik");
		 $storeHourInfo = array('Monday' => array('status' => $data['days_status']['mon'], 'start' => $data['start']['mon'], 'end' =>$data['end']['mon'], 'interval' => $data['interval']['mon']),
                               'Tuesday'=> array('status' => $data['days_status']['tue'], 'start' => $data['start']['tue'], 'end' =>$data['end']['tue'], 'interval' => $data['interval']['tue']),
                               'Wednesday'=> array('status' => $data['days_status']['wed'], 'start' => $data['start']['wed'], 'end' =>$data['end']['wed'], 'interval' => $data['interval']['wed']),
                               'Thursday'=> array('status' => $data['days_status']['thu'], 'start' => $data['start']['thu'], 'end' =>$data['end']['thu'], 'interval' => $data['interval']['thu']),
                               'Friday'=> array('status' => $data['days_status']['fri'], 'start' => $data['start']['fri'], 'end' =>$data['end']['fri'], 'interval' => $data['interval']['fri']),
                               'Saturday'=> array('status' => $data['days_status']['sat'], 'start' => $data['start']['sat'], 'end' =>$data['end']['sat'], 'interval' => $data['interval']['sat']),
                               'Sunday'=> array('status' => $data['days_status']['sun'], 'start' => $data['start']['sun'], 'end' =>$data['end']['sun'], 'interval' => $data['interval']['sun'])
                               ); 
	//	print_r($storeHourInfo);die("glh");
		 $hourInfo = json_encode($storeHourInfo);
		//$resultRedirect = $this->resultRedirectFactory->create();
	//	print_r($data);die("ljl");
        if ($data) {
            $model = Mage::getModel('storepickup/storepickup');
            $id = $this->getRequest()->getParam('pickup_id');
          //   echo $id; die("lngh");
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
    				//$model->setData('start',$val['start']);
    				//$model->setData('end',$val['end']);
    				//$model->setData('interval',$val['interval']);
    				//$model->setData('status',$val['status']); 
    				$model->save();
    				
    				/* $coll = $this->_objectManager->create('Ced\StorePickup\Model\StoreHour');
    				$coll = $coll->getCollection()
    					 ->addFieldToFilter('pickup_id',$id)
    					 ->getData();
    			
    				foreach($coll as $val){
    					$deleteObject = $this->_objectManager->create('Ced\StorePickup\Model\StoreHour');
    					$deleteObject->load($val['id']);
    					$deleteObject->delete();
    				}
    				*/
    			/* 	if(isset($storeHourInfo))
	    			{
	    				foreach ($storeHourInfo as $key => $val)
	    				{
	    					//$storeObject = $this->_objectManager->create('Ced\StorePickup\Model\StoreHour');
	    					$model->setData('pickup_id',$id);
	    					$model->setData('days',$key);
	    					$model->setData('start',$val['start']);
	    					$model->setData('end',$val['end']);
	    					$model->setData('interval',$val['interval']);
	    					$model->setData('status',$val['status']);
	    					$model->save();
	    				}
	    			}  */
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
    				//$model->setData('start',$val['start']);
    				//$model->setData('end',$val['end']);
    				//$model->setData('interval',$val['interval']);
    				//$model->setData('status',$val['status']);
    				
    				$model->save(); 
    				//$lastID = $model->getPickupId();
    				//echo $lastID;die("khkvb");
    			
    				//print_r($storeHourInfo->getData()); die("hkk");
    			/* if(isset($storeHourInfo)){
    				foreach ($storeHourInfo as $key => $val){
    				//	$storeObject = $this->_objectManager->create('Ced\StorePickup\Model\StoreHour');
    					$model->setData('pickup_id',$lastID);
    					$model->setData('days',$key);
    					$model->setData('start',$val['start']);
    					$model->setData('end',$val['end']);
    					$model->setData('interval',$val['interval']);
    					$model->setData('status',$val['status']);
    					$model->save();
    				}
			     }  */
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
       $this->_forward('index');
    }
    public function deleteAction()
    {
    	$pickup_ids = $this->getRequest()->getParam('pickup_id');
    	
    	
    	foreach ($pickup_ids as $pickup_id)
    	{
    	
    		try {
    			   Mage::getModel('storepickup/storepickup')->load($pickup_id)->delete();
    		      
    				
    		} catch (Exception $e) {
    			$this->messageManager->addError($e->getMessage());
    		} 
    		
    	}
    	
    	
    	Mage::getSingleton('adminhtml/session')->addSuccess(__('The store has been successfully Deleted.'));
    	return $this->_redirect('*/*/');
    	
    }
	
    public function massstatusAction()
    {
    	$postData = $this->getRequest()->getParams();
    	$pickupIds = $postData['pickup_id'];
    	$status = $postData['status'];
    	
    	foreach ($pickupIds as $id)
    	{
    		try {
    			if($status == 'Enable')
    			{
    				Mage::getModel('storepickup/storepickup')->load($id)->setIsActive(1)->save();
    			}
    			else{
    				Mage::getModel('storepickup/storepickup')->load($id)->setIsActive(0)->save();
    			}
    			
    		
    		
    		} catch (Exception $e) {
    			$this->messageManager->addError($e->getMessage());
    		}
    	}
    	
    	
    	Mage::getSingleton('adminhtml/session')->addSuccess(__('The store has been successfully '.$status.'d' ));
    	return $this->_redirect('*/*/');
    }
    
}