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
  * @package     Ced_CsVendorProductAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsVendorProductAttribute_AttributeController extends Ced_CsMarketplace_Controller_AbstractController
{
	/*
	 * Get the value of customer session
	 */
	public function _getSession() {
		return Mage::getSingleton('customer/session');
	}
	
	public function preDispatch()
	{
		parent::preDispatch();
		$this->_entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
	}
	public function indexAction(){
	   if(!$this->_getSession()->getVendorId())
			return;   
	   $enable=Mage::getStoreConfig('ced_csmarketplace/general/vattributes');
	   if($enable=='1'){
        $this->loadLayout();
        $this->_initLayoutMessages ('customer/session');
	    $this->renderLayout();
	   }
	   else{
	     $this->_redirect('csmarketplace/vendor/');
	   }
	
   }
    /*
     * Load all the grid action through Ajax
     */
	public function gridAction() {
			
		$this->loadLayout();
		$this->getResponse()->setBody($this->getLayout()->createBlock('csvendorproductattribute/attribute_grid')->toHtml());
			
	}
	
	public function newAction(){
		if(!$this->_getSession()->getVendorId())
			return;
		
	    $this->_forward('edit');
	}
	
	protected function _initAction()
	{
		$this->_title($this->__('Vendor Product Attribute'))
		->_title($this->__('Attributes'))
		->_title($this->__('Manage Attributes'));
	    return $this;
	}
	/*
	 * Edit the attribute Form
	 */
	public function editAction()
	{
		$enable=Mage::getStoreConfig('ced_csmarketplace/general/vattributes');
		if($enable=='1'){
     	$id = $this->getRequest()->getParam('attribute_id');
		
		$model = Mage::getModel('catalog/resource_eav_attribute')
		->setEntityTypeId($this->_entityTypeId);
		
		if ($id) {
			
			$model->load($id);
		
			if (! $model->getId()) {
				Mage::getSingleton('customer/session')->addError(
				Mage::helper('catalog')->__('This attribute no longer exists'));
				$this->_redirect('csvendorproductattribute/attribute/');
				return;
			}
		
			// entity type check
			if ($model->getEntityTypeId() != $this->_entityTypeId) {
				Mage::getSingleton('customer/session')->addError(
				Mage::helper('catalog')->__('This attribute cannot be edited.'));
				$this->_redirect('csvendorproductattribute/attribute/');
				return;
			}
		}
		
		// set entered data if was error when we do save
		$data = Mage::getSingleton('customer/session')->getAttributeData(true);
		if (! empty($data)) {
			$model->addData($data);
		}
	   Mage::register('entity_attribute', $model);
		
		$this->_initAction();
        $this->loadLayout();
        $this->_initLayoutMessages ( 'customer/session' );
		$this->renderLayout();
		}
		else{
			$this->_redirect('csmarketplace/vendor/');
		}
	}

	public function validateAction()
	{
	    $response = new Varien_Object();
		$response->setError(false);
	
		$attributeCode  = $this->getRequest()->getParam('attribute_code');
		$attributeId    = $this->getRequest()->getParam('attribute_id');
	
	
		$attribute = Mage::getModel('catalog/resource_eav_attribute')
		->loadByCode($this->_entityTypeId, $attributeCode);
	
		if ($attribute->getId() && !$attributeId) {
			Mage::getSingleton('customer/session')->addError(
			Mage::helper('catalog')->__('Attribute with the same code already exists'));
			$this->_initLayoutMessages('customer/session');
			$response->setError(true);
			$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
		}
	
		$this->getResponse()->setBody($response->toJson());
	
	}
	protected function _filterPostData($data)
	{
	
		if ($data) {
			/** @var $helperCatalog Mage_Catalog_Helper_Data */
			$helperCatalog = Mage::helper('catalog');
			//labels
			foreach ($data['frontend_label'] as & $value) {
				if ($value) {
					$value = $helperCatalog->stripTags($value);
				}
			}
	
			if (!empty($data['option']) && !empty($data['option']['value']) && is_array($data['option']['value'])) {
				foreach ($data['option']['value'] as $key => $values) {
					$data['option']['value'][$key] = array_map(array($helperCatalog, 'stripTags'), $values);
				}
			}
		}
		return $data;
	}
	/*
	* Save the attribute of vendor
	*/
	public function saveAction()
	{
		
		
		$data = $this->getRequest()->getPost();
		
		//Get vendor Id from Session
	    $vendor_id=$this->_getSession()->getVendorId();
	    $csmarket_vendor=Mage::getModel('csmarketplace/vendor')->load($vendor_id);
	    $vendor_code=$csmarket_vendor->getShop_url();
        $include_in_attribute_set= $this->getRequest()->getPost('include_in_attribute_set');
   		if ($data) {
			$session = Mage::getSingleton('catalog/session');
	
			$redirectBack   = $this->getRequest()->getParam('back', false);
			/* @var $model Mage_Catalog_Model_Entity_Attribute */
			$model = Mage::getModel('catalog/resource_eav_attribute');
			/* @var $helper Mage_Catalog_Helper_Product */
			$helper = Mage::helper('catalog/product');
			$vendor_model=Mage::getModel('csvendorproductattribute/attribute');
	
			$id = $this->getRequest()->getParam('attribute_id');
			
			//validate attribute_code
			if (isset($data['attribute_code'])) {
				
				$validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
				if (!$validatorAttrCode->isValid($data['attribute_code'])) {
					$session->addError(
							Mage::helper('catalog')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.')
					);
					$this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
					return;
				}
			}
	    	//validate frontend_input
			if (isset($data['frontend_input'])) {
			
				/** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
				$validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
				if (!$validatorInputType->isValid($data['frontend_input'])) {
					foreach ($validatorInputType->getMessages() as $message) {
						$session->addError($message);
					}
					$this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
					return;
				}
			}
	
			if ($id) {
			
				$model->load($id);
	           
				if (!$model->getId()) {
					$session->addError(
							Mage::helper('catalog')->__('This Attribute no longer exists'));
					$this->_redirect('*/*/index');
					return;
				}
	
				// entity type check
				if ($model->getEntityTypeId() != $this->_entityTypeId) {
					$session->addError(
							Mage::helper('catalog')->__('This attribute cannot be updated.'));
					$session->setAttributeData($data);
					$this->_redirect('*/*/index');
					return;
				}
	
				$data['attribute_code'] = $model->getAttributeCode();
				$data['is_user_defined'] = $model->getIsUserDefined();
				$data['frontend_input'] = $model->getFrontendInput();
				//Save the attribute in attribute set
				
			} else {
			
				/**
				 * @todo add to helper and specify all relations for properties
				 */
				$data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
				$data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
			}
	
			if (!isset($data['is_configurable'])) {
				$data['is_configurable'] = 0;
			}
			if (!isset($data['is_filterable'])) {
				$data['is_filterable'] = 0;
			}
			if (!isset($data['is_filterable_in_search'])) {
				$data['is_filterable_in_search'] = 0;
			}
	
			if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
				$data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
			}
	
			$defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
			if ($defaultValueField) {
				$data['default_value'] = $this->getRequest()->getParam($defaultValueField);
			}
	
			if(!isset($data['apply_to'])) {
				$data['apply_to'] = array();
			}
	
			//filter
			$data['include_in_attribute_set']=$include_in_attribute_set;
			$data = $this->_filterPostData($data);
		
			$model->addData($data);
			
			if (!$id) {
				$model->setEntityTypeId($this->_entityTypeId);
				$model->setIsUserDefined(1);
			}
	
	    
			if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
				
				// For creating product attribute on product page we need specify attribute set and group
				$model->setAttributeSetId($this->getRequest()->getParam('set'));
				$model->setAttributeGroupId($this->getRequest()->getParam('group'));
			}
	         
			try {
				$model->save();
		
				/*
				 * Save attribute in custom setting of vendor
				 */
				$attribute_id=$model->getId();
				$vendordata['attribute_id']=$attribute_id;
				$attribute_model=Mage::getModel('catalog/resource_eav_attribute')->load($attribute_id);
		
				$vendordata['attribute_code']=$attribute_model->getAttributeCode();
				$set_in_attribute=$attribute_model->getIncludeInAttributeSet();
				if(!$id){
			    $vendordata['vendor_id']=$vendor_id;
				
				$vendor_model->setData($vendordata);
			    
				$vendor_model->save();
				}
				
				/*
				 * Set the Attribute in Vendor Attribute Set
				 */
				if($include_in_attribute_set && $include_in_attribute_set=='1'){
			  	   	$setupmodel = Mage::getModel('eav/entity_setup','core_setup');
				    $attribute_set_name='vendor_'.$vendor_code;
				    $attributeSetId   = $setupmodel->getAttributeSetId('catalog_product',$attribute_set_name);
				    $attributeGroupId = $setupmodel->getAttributeGroupId('catalog_product',$attributeSetId,'General');
				    $setupmodel->addAttributeToSet('catalog_product',$attributeSetId,$attributeGroupId,$attribute_id);
				    Mage::helper('csvendorproductattribute')->__('Your product attribute has been saved to your aAttribute set.');
					
				}
			   $this->_getSession()->addSuccess(Mage::helper('csvendorproductattribute')->__('Your product attribute has been saved.'));
			
				
				/**
				 * Clear translation cache because attribute labels are stored in translation
				*/
				Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
				$session->setAttributeData(false);
				if ($this->getRequest()->getParam('popup')) {
					$this->_redirect('csvendorproductattribute/attribute/edit', array(
							'id'       => $this->getRequest()->getParam('product'),
							'attribute'=> $model->getId(),
							'_current' => true
					));
				} elseif ($redirectBack) {
					$this->_redirect('*/*/edit', array('attribute_id' => $model->getId(),'_current'=>true));
				} else {
					$this->_redirect('*/*/index', array());
				}
				return;
			} catch (Exception $e) {
			    $session->addError($e->getMessage());
				$session->setAttributeData($data);
				$this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
				return;
			}
		}
		
		$this->_redirect('*/*/index');
	}
	/**
	 * Delete the Attribute
	 */
	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('attribute_id')) {
			$model = Mage::getModel('catalog/resource_eav_attribute');
			/*
			 *Delete the entry from ced_vendor_product_attribute table 
			 */
			$vendor_model=Mage::getModel('csvendorproductattribute/attribute')->getCollection()->addFieldToFilter('attribute_id',$id)->getFirstItem();
			// entity type check
			$model->load($id);
			
			if ($model->getEntityTypeId() != $this->_entityTypeId) {
				Mage::getSingleton('customer/session')->addError(
				Mage::helper('catalog')->__('This attribute cannot be deleted.'));
				$this->_redirect('*/*/index');
				return;
			}
	
			try {
				$model->delete();
				$vendor_model->delete();
				Mage::getSingleton('customer/session')->addSuccess(
				Mage::helper('catalog')->__('The product attribute has been deleted.'));
				$this->_redirect('*/*/index');
				return;
			}
			catch (Exception $e) {
				Mage::getSingleton('customer/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
				return;
			}
		}
		Mage::getSingleton('customer/session')->addError(
		Mage::helper('catalog')->__('Unable to find an attribute to delete.'));
		$this->_redirect('*/*/index');
	}
	
	
}