<?php
/** * CedCommerce  *  * NOTICE OF LICENSE  *  * This source file is subject to the Academic Free License (AFL 3.0)  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt  * It is also available through the world-wide-web at this URL:  * http://opensource.org/licenses/afl-3.0.php  *  * @category    Ced  * @package     Ced_CsProduct  * @author   CedCommerce Core Team <connect@cedcommerce.com>  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)  */
/**
 * Catalog product controller
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <connect@cedcommerce.com>
 *
 */
require_once Mage::getModuleDir('controllers', 'Ced_CsMarketplace').DS.'VproductsController.php';
class Ced_CsProduct_VproductsController extends Ced_CsMarketplace_VproductsController {
	/**
	 * The greatest value which could be stored in CatalogInventory Qty field
	 */
	const MAX_QTY_VALUE = 99999999.9999;
	
	/**
	 * Array of actions which can be processed without secret key validation
	 * @var array
	 */
	protected $_publicActions = array('edit');

	protected $mode='';
	
	public function preDispatch() {
		parent::preDispatch();
		if(Mage::registry('ced_csproduct_current_store'))
			Mage::unRegister('ced_csproduct_current_store');
		Mage::register('ced_csproduct_current_store',Mage::app()->getStore()->getId());
	}
	
	protected function _redirect($path,$arguments = array()) {
		if(Mage::registry('ced_csproduct_current_store')) {
			$currentStoreId = Mage::registry('ced_csproduct_current_store');
			Mage::app()->setCurrentStore($currentStoreId);
		}
		parent::_redirect($path,$arguments);
	}
	
	protected function _redirectReferer($defaultUrl=null) {
		if(Mage::registry('ced_csproduct_current_store')) {
			$currentStoreId = Mage::registry('ced_csproduct_current_store');
			Mage::app()->setCurrentStore($currentStoreId);
		}
		parent::_redirect($defaultUrl);
	}
	
	/**
	 * Initialize product from request parameters
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _initProduct(){
		if(!$this->_getSession()->getVendorId())
			return;
		$this->_title($this->__('Catalog'))
		->_title($this->__('Manage Products'));
		
		$productId  = (int) $this->getRequest()->getParam('id');
		
		if ($productId){
			$this->mode=Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE;
		}
		else{
			$this->mode=Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE;
		}
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$product = Mage::getModel('catalog/product');
				
		if (!$productId) {
				$product->setStoreId(0);
				if ($setId = (int) $this->getRequest()->getParam('set')) {
					$product->setAttributeSetId($setId);
				}
				if ($typeId = $this->getRequest()->getParam('type')) {
					$product->setTypeId($typeId);
				}
		}
		$product->setData('_edit_mode', true);
		if ($productId) {
			try {
				$product->setStoreId($this->getRequest()->getParam('store', 0))->load($productId);
			} catch (Exception $e) {
				$product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
				Mage::logException($e);
			}
		}
		$attributes = $this->getRequest()->getParam('attributes');
		if ($attributes && $product->isConfigurable() &&
				(!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
					$product->getTypeInstance()->setUsedProductAttributeIds(
							explode(",", base64_decode(urldecode($attributes)))
			);
		}
		
		// Required attributes of simple product for configurable creation
		if ($this->getRequest()->getParam('popup')
				&& $requiredAttributes = $this->getRequest()->getParam('required')) {
					$requiredAttributes = explode(",", $requiredAttributes);
					foreach ($product->getAttributes() as $attribute) {
						if (in_array($attribute->getId(), $requiredAttributes)) {
							$attribute->setIsRequired(1);
						}
					}
		}
		
		if ($this->getRequest()->getParam('popup')
				&& $this->getRequest()->getParam('product')
				&& !is_array($this->getRequest()->getParam('product'))
				&& $this->getRequest()->getParam('id', false) === false) {
		
					$configProduct = Mage::getModel('catalog/product')
					->setStoreId(0)
					->load($this->getRequest()->getParam('product'))
					->setTypeId($this->getRequest()->getParam('type'));
		
					/* @var $configProduct Mage_Catalog_Model_Product */
					$data = array();
					foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {
		
						/* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
						if(!$attribute->getIsUnique()
								&& $attribute->getFrontend()->getInputType()!='gallery'
								&& $attribute->getAttributeCode() != 'required_options'
								&& $attribute->getAttributeCode() != 'has_options'
								&& $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
									$data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
								}
					}
		
					$product->addData($data)
					->setWebsiteIds($configProduct->getWebsiteIds());
		}
		
		Mage::register('product', $product);
		Mage::register('current_product', $product);
		Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
		$this->setCurrentStore();
		return $product;
		
	}
	
	/**
	 * Validate Allowed Attribute Set And Product Type
	 */
	public function validateSetAndType($type='',$set=0){
		$allowedType = Mage::getModel('csmarketplace/system_config_source_vproducts_type')->getAllowedType($this->getRequest()->getParam('store'));
		$allowedSet = Mage::getModel('csmarketplace/system_config_source_vproducts_set')->getAllowedSet($this->getRequest()->getParam('store'));
		$secretkey=time();
		if($type=='')
			$type = $this->getRequest()->getParam ('type',$secretkey);
		if($set==0)
			$set= (int)$this->getRequest()->getParam ('set',0);
		if ($type==$secretkey||(in_array($type,$allowedType) && in_array($set,$allowedSet))) {
			return true;
		}
		return false;
	}

	
	/**
	 * Create serializer block for a grid
	 *
	 * @param string $inputName
	 * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
	 * @param array $productsArray
	 * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
	 */
	protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
	{
		return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
		->setGridBlock($gridBlock)
		->setProducts($productsArray)
		->setInputElementName($inputName)
		;
	}
	
	/**
	 * Output specified blocks as a text list
	 */
	protected function _outputBlocks()
	{
		$blocks = func_get_args();
		$output = $this->getLayout()->createBlock('adminhtml/text_list');
		foreach ($blocks as $block) {
			$output->insert($block, '', true);
		}
		$this->getResponse()->setBody($output->toHtml());
	}
	
	/**
	 * Product list page
	 */
	public function indexAction()
	{
		if(!$this->_getSession()->getVendorId()) 
			return;
		if(Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId()) && $this->getRequest()->getModuleName()=='csmarketplace') {
			$this->_redirect('csproduct/vproducts/index');
			return;
		}
		else if(!Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId())){
			if($this->getRequest()->getModuleName()=='csproduct'){
				$this->_redirect('csmarketplace/vproducts/index');
				return;
			}
			else if($this->getRequest()->getModuleName()=='csmarketplace'){
				parent::indexAction();
				return;
			}
		}
		
		$this->loadLayout();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );
		$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('csproduct/vproducts/');
		}
		$this->getLayout()->getBlock('head')->setTitle($this->__('Manage Products'));
		$this->renderLayout();
	}
	
	/**
	 * Create new product page
	 */
	public function newAction()
	{
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		if(Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId()) && $this->getRequest()->getModuleName()=='csmarketplace') {
			$this->_redirect('csproduct/vproducts/new');
			return;
		}
		else if(!Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId())){
				if($this->getRequest()->getModuleName()=='csproduct'){
					$this->_redirect('csmarketplace/vproducts/new');
					return;
				}
				else if($this->getRequest()->getModuleName()=='csmarketplace'){
					parent::newAction();
					return;					
				}
		}
		
		if(count(Mage::getModel('csmarketplace/vproducts')->getVendorProductIds($this->_getSession()->getVendorId()))>=Mage::helper('csmarketplace')->getVendorProductLimit()){
			$this->_getSession()->addError($this->__('Product Creation limit has Exceeded'));
			$this->_redirect('*/*/index',array('store'=>$this->getRequest()->getParam('store', 0)));
			return;
		}
		
		if(!$this->validateSetAndType()){
			$this->_redirect('*/*/new');
			return;
		}
		
		$product = $this->_initProduct();
		$this->_title(null);
		$this->_title($this->__('New Product'));
		
		Mage::dispatchEvent('catalog_product_new_action', array('product' => $product));
	
		if ($this->getRequest()->getParam('popup')) {
			$this->loadLayout('popup');
		} else {
			$_additionalLayoutPart = '';
			if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
					&& !($product->getTypeInstance()->getUsedProductAttributeIds()))
			{
				$_additionalLayoutPart = '_new';
			}
			
			$this->loadLayout(array(
					'default',
					strtolower($this->getFullActionName()),
					'csproduct_vproducts_'.$product->getTypeId().$_additionalLayoutPart,
			));				
						
			$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
			if ($navigationBlock) {
				$navigationBlock->setActive('csproduct/vproducts/new');
			}
		}
		
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );
			
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		
		$block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
		if ($block) {
			$block->setStoreId($product->getStoreId());
		}
	
		$this->renderLayout();
	}

	
	/**
	 * Product edit form
	 */
	public function editAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId) {
			return;
		}
		
		if(Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId()) && $this->getRequest()->getModuleName()=='csmarketplace') {
			$this->_redirect('csproduct/vproducts/edit');
			return;
		}
		if(!Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId())){
				if($this->getRequest()->getModuleName()=='csproduct'){
					$this->_redirect('csmarketplace/vproducts/edit');
					return;
				}
				else if($this->getRequest()->getModuleName()=='csmarketplace'){
					parent::editAction();
					return;					
				}
		}
		
		$productId  = (int) $this->getRequest()->getParam('id');
		$vendorProduct=false;
		if($productId && $vendorId){
			$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$productId);
		}
		if(!$vendorProduct){
			$this->_redirect ( 'csmarketplace/vproducts/index');
			return;
		}
			
		$product = $this->_initProduct();	
		if ($productId && !$product->getId()) {
			$this->_getSession()->addError(Mage::helper('catalog')->__('This product no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$this->_title(null);
		$this->_title($product->getName());
	
		Mage::dispatchEvent('catalog_product_edit_action', array('product' => $product));
	
		$_additionalLayoutPart = '';
		if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
				&& !($product->getTypeInstance()->getUsedProductAttributeIds()))
		{
			$_additionalLayoutPart = '_new';
		}
		
		$this->loadLayout(array(
				'default',
				strtolower($this->getFullActionName()),
				'csproduct_vproducts_'.$product->getTypeId().$_additionalLayoutPart,
		));
		
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );
	
		$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('csproduct/vproducts/');
		}
	
		if ($switchBlock = $this->getLayout()->getBlock('store_switcher')) {
			$switchBlock->setDefaultStoreName($this->__('Default Values'))
			->setWebsiteIds($product->getWebsiteIds())
			->setSwitchUrl(
					Mage::getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
			);
		}
	
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		
		$block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
		if ($block) {
			$block->setStoreId($product->getStoreId());
		}
	
		$this->renderLayout();
	}
	
	/**
	 * WYSIWYG editor action for ajax request
	 *
	 */
	public function wysiwygAction()
	{
		if(!$this->_getSession()->getVendorId()) 
			return;
		$elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
		$storeId = $this->getRequest()->getParam('store_id', 0);
		$storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
	
		$content = $this->getLayout()->createBlock('csproduct/edit_form_wysiwyg_content', '', array(
				'editor_element_id' => $elementId,
				'store_id'          => $storeId,
				'store_media_url'   => $storeMediaUrl,
		));
		$this->getResponse()->setBody($content->toHtml());
	}
	
	/**
	 * Product grid for AJAX request
	 */
	public function gridAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Get specified tab grid
	 */
	public function gridOnlyAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()
				->createBlock('adminhtml/catalog_product_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
				->toHtml()
		);
	}
	
	/**
	 * Get options fieldset block
	 *
	 */
	public function optionsAction()
	{
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Get related products grid and serializer block
	 */
	public function relatedAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.related')
		->setProductsRelated($this->getRequest()->getPost('products_related', null));
		$this->renderLayout();
	}
	
	/**
	 * Get upsell products grid and serializer block
	 */
	public function upsellAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.upsell')
		->setProductsUpsell($this->getRequest()->getPost('products_upsell', null));
		$this->renderLayout();
	}
	
	/**
	 * Get crosssell products grid and serializer block
	 */
	public function crosssellAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
		->setProductsCrossSell($this->getRequest()->getPost('products_crosssell', null));
		$this->renderLayout();
	}
	
	/**
	 * Get related products grid
	 */
	public function relatedGridAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.related')
		->setProductsRelated($this->getRequest()->getPost('products_related', null));
		$this->renderLayout();
	}
	
	/**
	 * Get upsell products grid
	 */
	public function upsellGridAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.upsell')
		->setProductsRelated($this->getRequest()->getPost('products_upsell', null));
		$this->renderLayout();
	}
	
	/**
	 * Get crosssell products grid
	 */
	public function crosssellGridAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
		->setProductsRelated($this->getRequest()->getPost('products_crosssell', null));
		$this->renderLayout();
	}
	
	/**
	 * Get associated grouped products grid and serializer block
	 */
	public function superGroupAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.super.group')
		->setProductsGrouped($this->getRequest()->getPost('products_grouped', null));
		$this->renderLayout();
	}
	
	/**
	 * Get associated grouped products grid only
	 *
	 */
	public function superGroupGridOnlyAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.super.group')
		->setProductsGrouped($this->getRequest()->getPost('products_grouped', null));
		$this->renderLayout();
	}
	
	/**
	 * Get super config grid
	 *
	 */
	public function superConfigAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->_initProduct();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('csproduct/edit_tab_super_config_grid')->toHtml()
		);
	}
	
	/**
	 * Validate product
	 *
	 */
	public function validateAction()
	{
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		$response = new Varien_Object();
		$response->setError(false);
	
		try {
			$errors=array();
			$productData = $this->getRequest()->getPost('product');
	
			if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
				$productData['stock_data']['use_config_manage_stock'] = 0;
			}
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$product = Mage::getModel('catalog/product');
			$product->setData('_edit_mode', true);
			if ($storeId = $this->getRequest()->getParam('store')) {
				$product->setStoreId($storeId);
			}
			else{
				$product->setStoreId(0);
			}
			if ($setId = $this->getRequest()->getParam('set')) {
				$product->setAttributeSetId($setId);
			}
			if ($typeId = $this->getRequest()->getParam('type')) {
				$product->setTypeId($typeId);
			}
			if ($productId = $this->getRequest()->getParam('id')) {
				$product->load($productId);
			}
	
			$dateFields = array();
			$attributes = $product->getAttributes();
			foreach ($attributes as $attrKey => $attribute) {
				if ($attribute->getBackend()->getType() == 'datetime') {
					if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != ''){
						$dateFields[] = $attrKey;
					}
				}
			}
			$productData = $this->_filterDates($productData, $dateFields);
			$product->addData($productData);
			$product->validate();
			$this->setCurrentStore();
			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			*/
			//            if (is_array($errors = $product->validate())) {
			//                foreach ($errors as $code => $error) {
			//                    if ($error === true) {
			//                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is invalid.', $product->getResource()->getAttribute($code)->getFrontend()->getLabel()));
			//                    }
			//                    else {
			//                        Mage::throwException($error);
			//                    }
			//                }
			//            }
			
		}
		catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
			$errors[]=$e->getMessage();
		}
		catch (Mage_Core_Exception $e) {
			$errors[]=$e->getMessage();
		}
		catch (Exception $e) {
			$errors[]=$e->getMessage();
		}
		
		$productData = $this->getRequest()->getPost('product');
		$vproductModel = Mage::getModel('csmarketplace/vproducts');
		$vproductModel->addData(isset($productData)?$productData:'');
		$vproductModel->addData(isset($productData['stock_data'])?$productData['stock_data']:'');
		if ($typeId = $product->getTypeId()) {
			$vproductModel->setType($typeId);
		}
		$productErrors=$vproductModel->validate($product);
		if (is_array($productErrors) && count($productErrors)) {
			$errors = array_merge($errors, $productErrors);
		}
		if (count($errors)) {
			$this->_initLayoutMessages('customer/session');
			$response->setError(true);
			$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
			$message='<ul>';
			foreach ($errors as $error) {
				$message=$message.'<li>'.$error.'</li>';
			}
			$message=$message.'</ul>';
			$response->setMessage($message);
		}
		$this->getResponse()->setBody($response->toJson());
	}
	
	/**
	 * Initialize product before saving
	 */
	protected function _initProductSave()
	{
		$product    = $this->_initProduct();
		$productData = $this->getRequest()->getPost('product');
	 	if ($productData) {
            $this->_filterStockData($productData['stock_data']);
        }
	
		/**
		 * Websites
		 */
		if (!isset($productData['website_ids'])) {
			$productData['website_ids'] = array();
		}
	
		$wasLockedMedia = false;
		if ($product->isLockedAttribute('media')) {
			$product->unlockAttribute('media');
			$wasLockedMedia = true;
		}
	
		if($this->mode==Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE){
			if(isset($productData['status']) && !Mage::getModel('csmarketplace/vproducts')->isApproved($product->getId())){
				unset($productData['status']);
			}
		}
		
		$product->addData($productData);
		if($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE){
			if(Mage::getModel('csmarketplace/vproducts')->isProductApprovalRequired())
				$product->setStatus (Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
		}

		if ($wasLockedMedia) {
			$product->lockAttribute('media');
		}
	
		if ((Mage::app()->isSingleStoreMode() && Mage::helper('csmarketplace')->isSharingEnabled())||!Mage::helper('csmarketplace')->isSharingEnabled()) {
			$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
		}
	
		/**
		 * Create Permanent Redirect for old URL key
		 */
		if ($product->getId() && isset($productData['url_key_create_redirect']))
		// && $product->getOrigData('url_key') != $product->getData('url_key')
		{
			$product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
		}
	
		/**
		 * Check "Use Default Value" checkboxes values
		 */
		if ($useDefaults = $this->getRequest()->getPost('use_default')) {
			foreach ($useDefaults as $attributeCode) {
				$product->setData($attributeCode, false);
			}
		}
	
		/**
		 * Init product links data (related, upsell, crosssel)
		 */
		
		$links = $this->getRequest()->getPost('links');
		if (isset($links['related']) && !$product->getRelatedReadonly()) {
			$product->setRelatedLinkData(Mage::getSingleton('csmarketplace/vproducts')->unsetInvalidData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related'])));
		}
		if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
			$product->setUpSellLinkData(Mage::getSingleton('csmarketplace/vproducts')->unsetInvalidData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell'])));
		}
		if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
			$product->setCrossSellLinkData(Mage::getSingleton('csmarketplace/vproducts')->unsetInvalidData(Mage::helper('adminhtml/js')
					->decodeGridSerializedInput($links['crosssell'])));
		}
		if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
			$product->setGroupedLinkData(Mage::getSingleton('csmarketplace/vproducts')->unsetInvalidData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped'])));
		}
		
	
		/**
		 * Initialize product categories
		 */
		$categoryIds = $this->getRequest()->getPost('category_ids');
		if (null !== $categoryIds) {
			if (empty($categoryIds)) {
				$categoryIds = array();
			}
			$product->setCategoryIds($categoryIds);
		}
	
		/**
		 * Initialize data for configurable product
		 */
		
		if (($data = $this->getRequest()->getPost('configurable_products_data')) && !$product->getConfigurableReadonly()) {
					$product->setConfigurableProductsData(Mage::getSingleton('csmarketplace/vproducts')->unsetInvalidData(Mage::helper('core')->jsonDecode($data)));
		}
		if (($data = $this->getRequest()->getPost('configurable_attributes_data')) 
				&& !$product->getConfigurableReadonly()) {
					$product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
		}
	
		$product->setCanSaveConfigurableAttributes((bool)$this->getRequest()->getPost('affect_configurable_product_attributes')
				&& !$product->getConfigurableReadonly()
				);
	
		/**
		* Initialize product options
		*/
		if (isset($productData['options']) && !$product->getOptionsReadonly()) {
			$product->setProductOptions($productData['options']);
		}
	
		$product->setCanSaveCustomOptions(
			(bool)$this->getRequest()->getPost('affect_product_custom_options')
			&& !$product->getOptionsReadonly()
		);
		
		Mage::dispatchEvent(
		'catalog_product_prepare_save',
			array('product' => $product, 'request' => $this->getRequest())
		);
	
		return $product;
	}
	
	/**
	 * Filter product stock data
	 *
	 * @param array $stockData
	 * @return null
	 */
	protected function _filterStockData(&$stockData)
	{
		if (is_null($stockData)) {
			return;
		}
		if (!isset($stockData['use_config_manage_stock'])) {
			$stockData['use_config_manage_stock'] = 0;
		}
		if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
			$stockData['qty'] = self::MAX_QTY_VALUE;
		}
		if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
			$stockData['min_qty'] = 0;
		}
		if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
			$stockData['is_decimal_divided'] = 0;
		}
	}
	
	public function categoriesJsonAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$product = $this->_initProduct();
	
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('csproduct/edit_tab_categories')
				->getCategoryChildrenJson($this->getRequest()->getParam('category'))
		);
	}
	
	/**
	 * Save product action
	 */
	public function saveAction()
	{			if(Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId())){
			$vendorId=$this->_getSession()->getVendorId();
			if(!$vendorId) {
				return;
			}
			
			$storeId        = $this->getRequest()->getParam('store');
			$redirectBack   = $this->getRequest()->getParam('back', false);
			$productId      = $this->getRequest()->getParam('id');
			$isEdit         = (int)($this->getRequest()->getParam('id') != null);
			$vendorProduct=false;
			if($productId && $vendorId){
				$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$productId);
				if(!$vendorProduct){
					$this->_redirect('*/*/', array('store'=>$storeId));
					return;
				}
			}
			else{		
				if(count(Mage::getModel('csmarketplace/vproducts')->getVendorProductIds($this->_getSession()->getVendorId()))>=Mage::helper('csmarketplace')->getVendorProductLimit()){
					$this->_getSession()->addError($this->__('Product Creation limit has Exceeded'));
					$this->_redirect('*/*/index', array('store'=>$storeId));
					return;
				}
				
				if(!$this->validateSetAndType()){
					$this->_redirect('*/*/new');
					return;
				}
			}
			
			$data = $this->getRequest()->getPost();
			if ($data) {
				$stock_data = isset($data['product']['stock_data'])?$data['product']['stock_data']:array();
				$this->_filterStockData($stock_data);
				
				$product = $this->_initProductSave();
		
				try {
					$currentStore=Mage::app()->getStore()->getId();
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$product=$product->save();										if($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE) {						Mage::dispatchEvent('new_vendor_product_creation',array('product' => $product, 'vendor_id'=>$this->_getSession()->getVendorId())						);					}
					Mage::app()->setCurrentStore($currentStore);
					if($product->getId()){
						$productId = $product->getId();
						Mage::register('saved_product',$product);
						if(Mage::getModel('csmarketplace/vproducts')->setStoreId($product->getStoreId())->saveProduct($this->mode)>0){
							/**
							** Do copying data to stores
							*/
							if (isset($data['copy_to_stores'])) {
								$this->_copyAttributesBetweenStores($data['copy_to_stores'], $product);
							}
						
							Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);
							$this->_getSession()->addSuccess(Mage::helper ('csmarketplace')->__('The product has been saved.'));
						}
					}
				
				}
				catch (Mage_Core_Exception $e) {
					$this->_getSession()->addError($e->getMessage())
					->setProductData($data);
					$redirectBack = true;
				}
				catch (Exception $e) {
					Mage::logException($e);
					$this->_getSession()->addError($e->getMessage());
					$redirectBack = true;
				}
			}
			
			if ($redirectBack) {
				$this->_redirect('*/*/edit', array(
						'id'    => $productId,
						'_current'=>true
				));
			}
			else if($this->getRequest()->getParam('popup')) {
				$this->_redirect('*/*/created', array(
						'_current'   => true,
						'id'         => $productId,
						'edit'       => $isEdit
				));
			}
			else {
				$this->_redirect('*/*/index', array('store'=>$storeId));
			}		}		else		{			$configuration =Mage::getControllerInstance('Ced_CsMarketplace_VproductsController', $this->getRequest(), $this->getResponse());			$configuration->dispatch('save');		}
	}
	
	
	/**
	 * Duplicates product attributes between stores.
	 * @param array $stores list of store pairs: array(fromStore => toStore, fromStore => toStore,..)
	 * @param Mage_Catalog_Model_Product $product whose attributes should be copied
	 * @return $this
	 */
	protected function _copyAttributesBetweenStores(array $stores, Mage_Catalog_Model_Product $product)
	{
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		foreach ($stores as $storeTo => $storeFrom) {
			$productInStore = Mage::getModel('catalog/product')
			->setStoreId($storeFrom)
			->load($product->getId());
			Mage::dispatchEvent('product_duplicate_attributes', array(
			'product' => $productInStore,
			'storeTo' => $storeTo,
			'storeFrom' => $storeFrom,
			));
			$productInStore->setStoreId($storeTo)->save();
		}
		$this->setCurrentStore();
		return $this;
	}
	
	/**
	 * Delete product action
	 */
	public function deleteAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		if(!$vendorId) 
			return;
		$id=$this->getRequest()->getParam('id');
		$vendorProduct=false;
		if($id && $vendorId){
			$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$id);
		}
		
		if(!$vendorProduct){
			$redirectBack=true;
		}
		else if ($id) {
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$product = Mage::getModel('catalog/product')
			->load($id);
			$sku = $product->getSku();
			try {
				if($product && $product->getId()) {
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$product->delete();
					Mage::getModel('csmarketplace/vproducts')->changeVproductStatus(array($id),Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
					$this->_getSession()->addSuccess(Mage::helper('csmarketplace')->__('Your Product Has Been Sucessfully Deleted'));
				}				
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
			$this->setCurrentStore();
		}
		if ($redirectBack) {
			$this->_getSession()->addError( Mage::helper('csmarketplace')->__('Unable to delete the product'));
		}
		$this->getResponse()
		->setRedirect(Mage::getUrl('*/*/index', array('store'=>$this->getRequest()->getParam('store'))));
	}
	
	
	public function createdAction()
	{
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		$this->_getSession()->addNotice(
				Mage::helper('catalog')->__('Please click on the Close Window button if it is not closed automatically.')
		);
		$this->loadLayout('popup');
		$this->_addContent(
				$this->getLayout()->createBlock('adminhtml/catalog_product_created')
		);
		$this->renderLayout();
	}
	
	public function massDeleteAction()
	{
		$vendorId=$this->_getSession()->getVendorId();
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		if(!$vendorId)
			return;
				
		$productIds = explode(',',$this->getRequest()->getParam('product'));
		if (!is_array($productIds)) {
			$this->_getSession()->addError($this->__('Please select product(s).'));
		} else {
			if (!empty($productIds)) {
				try {
					$ids=array();
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					foreach ($productIds as $productId) {
						$vendorProduct=false;
						if($productId && $vendorId){
							$vendorProduct = Mage::getSingleton('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$productId);
						}
						if(!$vendorProduct)
							continue;
						$product = Mage::getSingleton('catalog/product')->load($productId);
						Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));	
						$product->delete();
						$ids[]=$productId;
					}
					Mage::getModel('csmarketplace/vproducts')->changeVproductStatus($ids,Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
					$this->setCurrentStore();
					$this->_getSession()->addSuccess(
							$this->__('Total of %d record(s) have been deleted.', count($ids))
					);
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		$this->_redirect('*/*/index', array('store'=> $storeId));
	}
	
	/**
	 * Update product(s) status action
	 *
	 */
	public function massStatusAction()
	{
		$ids = (array)$this->getRequest()->getParam('product');
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		$status     = (int)$this->getRequest()->getParam('status');
	
		try {
			$productIds=$this->_validateMassStatus($ids, $status);
			if(count(array_diff($productIds,$ids)))
				$this->_getSession()->addError(
						$this->__('Some of the processed products have not approved. Only Approved Products status can be changed.'));
			Mage::getSingleton('catalog/product_action')
			->updateAttributes($productIds, array('status' => $status), $storeId);
	
			$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) have been updated.', count($productIds))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()
			->addException($e, $this->__('An error occurred while updating the product(s) status.'));
		}
	
		$this->_redirect('*/*/', array('store'=> $storeId));
	}
	
	/**
	 * Validate batch of products before theirs status will be set
	 *
	 * @throws Mage_Core_Exception
	 * @param  array $productIds
	 * @param  int $status
	 * @return void
	 */
	public function _validateMassStatus(array $productIds, $status)
	{
		if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
			if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
				throw new Mage_Core_Exception(
						$this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.')
				);
			}
		}
		$approvedProducts=Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('check_status',array('eq'=>Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS))
							->addFieldToFilter('product_id',array('in'=>explode(',',$productIds[0])));
		$approvedIds=array();
		foreach ($approvedProducts as $row){
			$approvedIds[]=$row->getProductId();
		}
		return $approvedIds;
	}
	
	/**
	 * @return Mage_Adminhtml_Controller_Action
	 */
	protected function _addContent(Mage_Core_Block_Abstract $block)
	{
		$this->getLayout()->getBlock('content')->append($block);
		return $this;
	}
	
	public function quickCreateAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$result = array();
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		/* @var $configurableProduct Mage_Catalog_Model_Product */
		$configurableProduct = Mage::getModel('catalog/product')
		->setStoreId(0)
		->load($this->getRequest()->getParam('product'));
	
		if (!$configurableProduct->isConfigurable()) {
			// If invalid parent product
			$this->_redirect('*/*/');
			return;
		}
	
		/* @var $product Mage_Catalog_Model_Product */
	
		$product = Mage::getModel('catalog/product')
		->setStoreId(0)
		->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
		->setAttributeSetId($configurableProduct->getAttributeSetId());
	
	
		foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
			if ($attribute->getIsUnique()
					|| $attribute->getAttributeCode() == 'url_key'
					|| $attribute->getFrontend()->getInputType() == 'gallery'
					|| $attribute->getFrontend()->getInputType() == 'media_image'
					|| !$attribute->getIsVisible()) {
						continue;
					}
	
					$product->setData(
							$attribute->getAttributeCode(),
							$configurableProduct->getData($attribute->getAttributeCode())
					);
		}
	
		$product->addData($this->getRequest()->getParam('simple_product', array()));
		$product->setWebsiteIds($configurableProduct->getWebsiteIds());
	
		$autogenerateOptions = array();
		$result['attributes'] = array();
	
		foreach ($configurableProduct->getTypeInstance()->getConfigurableAttributes() as $attribute) {
			$value = $product->getAttributeText($attribute->getProductAttribute()->getAttributeCode());
			$autogenerateOptions[] = $value;
			$result['attributes'][] = array(
					'label'         => $value,
					'value_index'   => $product->getData($attribute->getProductAttribute()->getAttributeCode()),
					'attribute_id'  => $attribute->getProductAttribute()->getId()
			);
		}
	
		if ($product->getNameAutogenerate()) {
			$product->setName($configurableProduct->getName() . '-' . implode('-', $autogenerateOptions));
		}
	
		if ($product->getSkuAutogenerate()) {
			$product->setSku($configurableProduct->getSku() . '-' . implode('-', $autogenerateOptions));
		}
	
		if (is_array($product->getPricing())) {
			$result['pricing'] = $product->getPricing();
			$additionalPrice = 0;
			foreach ($product->getPricing() as $pricing) {
				if (empty($pricing['value'])) {
					continue;
				}
	
				if (!empty($pricing['is_percent'])) {
					$pricing['value'] = ($pricing['value']/100)*$product->getPrice();
				}
	
				$additionalPrice += $pricing['value'];
			}
	
			$product->setPrice($product->getPrice() + $additionalPrice);
			$product->unsPricing();
		}
	
		try {
			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			 */
			//            if (is_array($errors = $product->validate())) {
			//                $strErrors = array();
			//                foreach($errors as $code=>$error) {
			//                    $codeLabel = $product->getResource()->getAttribute($code)->getFrontend()->getLabel();
			//                    $strErrors[] = ($error === true)? Mage::helper('catalog')->__('Value for "%s" is invalid.', $codeLabel) : Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $codeLabel, $error);
			//                }
			//                Mage::throwException('data_invalid', implode("\n", $strErrors));
			//            }
	
			$product->validate();			
			$product->save();			/*echo Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE;die;*/			if($product->getId()){				$productId = $product->getId();				Mage::register('saved_product',$product);				Mage::getModel('csmarketplace/vproducts')->setStoreId($product->getStoreId())->saveProduct(Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE);			}
			$result['product_id'] = $product->getId();
			$this->_getSession()->addSuccess(Mage::helper('catalog')->__('The product has been created.'));
			$this->_initLayoutMessages('customer/session');
			$result['messages']  = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
		} catch (Mage_Core_Exception $e) {
			$result['error'] = array(
					'message' =>  $e->getMessage(),
					'fields'  => array(
							'sku'  =>  $product->getSku()
					)
			);
	
		} catch (Exception $e) {
			Mage::logException($e);
			$result['error'] = array(
					'message'   =>  $this->__('An error occurred while saving the product. ') . $e->getMessage()
			);
		}
		$this->setCurrentStore();
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
	/**
	 * Vendor product page category list action
	 */
	public function categorytreeAction(){
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		$data = $this->getRequest()->getParams();
		$category_model = Mage::getModel("catalog/category")->setStoreId($this->getRequest()->getParam('store',0));
		$category = $category_model->load($data["cd"]);
		$children = $category->getChildren();
		$all = explode(",",$children);
		$result_tree = "";
		$ml = $data["ml"]+20;
		$count = 1;
		$total = count($all);
		$plus = 0;
	
		$allowed_categories=array();
		$category_mode=0;
		$category_mode = Mage::getStoreConfig('ced_vproducts/general/category_mode',0);
		if($category_mode)
			$allowed_categories = explode(',',Mage::getStoreConfig('ced_vproducts/general/category',0));
		
		foreach($all as $each){
			$count++;
			$_category = $category_model->load($each);
			
			if($category_mode && !in_array($_category['entity_id'], $allowed_categories))
				continue;
			if($category_mode)
				$childrens=count(array_intersect($category_model->getResource()->getAllChildren($category_model->load($_category['entity_id'])),$allowed_categories))-1;
			else
				$childrens=count($category_model->getResource()->getAllChildren($category_model->load($_category['entity_id'])))-1;
			
			if($childrens > 0){
				$result[$plus]['counting']=1;
				$result[$plus]['id']= $_category['entity_id'];
				$result[$plus]['name']= $_category->getName();
				$result[$plus]['product_count']=Mage::getModel('csmarketplace/vproducts')->getProductCount($_category['entity_id']);
			}
			else{
				$result[$plus]['counting']=0;
				$result[$plus]['id']= $_category['entity_id'];
				$result[$plus]['name']= $_category->getName();
				$result[$plus]['product_count']=Mage::getModel('csmarketplace/vproducts')->getProductCount($_category['entity_id']);
			}
			$plus++;
		}
		echo json_encode($result);
	}
	
	public function setCurrentStore(){
		if(Mage::registry('ced_csproduct_current_store')) {
			$currentStoreId = Mage::registry('ced_csproduct_current_store');
			Mage::app()->setCurrentStore($currentStoreId);
		}
	}
}
