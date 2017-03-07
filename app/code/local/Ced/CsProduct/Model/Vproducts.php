<?php 
/** * CedCommerce * * NOTICE OF LICENSE * * This source file is subject to the Academic Free License (AFL 3.0) * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt * It is also available through the world-wide-web at this URL: * http://opensource.org/licenses/afl-3.0.php * * @category    Ced * @package     Ced_CsProduct * @author   CedCommerce Core Team <connect@cedcommerce.com> * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/) * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) */
/**
 * Vendor Product model
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <connect@cedcommerce.com>
 */
class Ced_CsProduct_Model_Vproducts extends Ced_CsMarketplace_Model_Vproducts {
		
	protected $_vproducts=array();
	
	/**
	 * Initialize vproducts model
	 */
	protected function _construct() {
		parent::_construct();
	}
	
	/**
	 * Check Product Admin Approval required
	 */
	public function isProductApprovalRequired(){
		return Mage::getStoreConfig('ced_vproducts/general/confirmation',Mage::app()->getStore()->getId());
	}
	
	/**
	 * Validate csmarketplace product attribute values.
	 * @return array $errors
	 */
	public function validate($product=null){		if(Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId())){
			$errors = array();
			$use_default=array();
			$use_default=Mage::app()->getRequest()->getParam('use_default');
			if($use_default==null)
				$use_default=array();
			$helper=Mage::helper('csmarketplace');
			
			if (!Zend_Validate::is( trim($this->getName()) , 'NotEmpty') && !in_array('name',$use_default)) {
				$errors[] = $helper->__('The Product Name cannot be empty');
			}
			if (!Zend_Validate::is( trim($this->getSku()) , 'NotEmpty')) {
				$errors[] = $helper->__('The Product SKU cannot be empty');
			}
			$shortDescription=trim($this->getShortDescription());
			$description=trim($this->getdescription());
			if (strlen($shortDescription)==0 && !in_array('short_description',$use_default)) {
				$errors[] = $helper->__('The Product Short description cannot be empty');
			}
			if (strlen($description)==0 && !in_array('description',$use_default)) {
				$errors[] = $helper->__('The Product Description cannot be empty');
			}
			
			
			if($this->getType()==Mage_Catalog_Model_Product_Type::TYPE_SIMPLE ){
				$weight=	trim($this->getWeight());
				if (!Zend_Validate::is($weight, 'NotEmpty')) {
					$errors[] = $helper->__('The Product Weight cannot be empty');
				}
				else if(!is_numeric($weight)&&!($weight>0))
					$errors[] = $helper->__('The Product Weight must be 0 or Greater');
			}
			
			if($this->getType()==Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
				$weighttype= trim($this->getWeightType());
				if (!Zend_Validate::is( trim($weighttype) , 'NotEmpty')) {
					$errors[] = $helper->__('The Product Weight Type cannot be empty');
				}
				else if($weighttype==Ced_CsProduct_Block_Edit_Form_Renderer_Bundle_Attributes_Extend::FIXED){
					$weight=	trim($this->getWeight());
					if (!Zend_Validate::is($weight, 'NotEmpty')) {
						$errors[] = $helper->__('The Product Weight cannot be empty');
					}
					else if(!is_numeric($weight)&&!($weight>0))
						$errors[] = $helper->__('The Product Weight must be 0 or Greater');
				}
				
				$skutype= trim($this->getSkuType());
				if (!Zend_Validate::is( trim($skutype) , 'NotEmpty')) {
					$errors[] = $helper->__('The Product SKU Type cannot be empty');
				}
				
				$pricetype= trim($this->getPriceType());
				if (!Zend_Validate::is( trim($pricetype) , 'NotEmpty') && !$product->getId()) {
					$errors[] = $helper->__('The Product Price Type cannot be empty');
				}
				else if($pricetype==Ced_CsProduct_Block_Edit_Form_Renderer_Bundle_Attributes_Extend::FIXED && !in_array('price',$use_default)){
					$price=trim($this->getPrice());
					if (!Zend_Validate::is( $price, 'NotEmpty') && !in_array('price',$use_default)) {
						$errors[] = $helper->__('The Product Price cannot be empty');
					}
					else if(!is_numeric($price)&&!($price>0) && !in_array('price',$use_default))
						$errors[] = $helper->__('The Product Price must be 0 or Greater');
					if (!Zend_Validate::is( trim($this->getTaxClassId()) , 'NotEmpty')  && !in_array('tax_class_id',$use_default)) {
						$errors[] = $helper->__('The Product Tax Class cannot be empty');
					}
				}		
			}
			
			if($this->getType()!=Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && $this->getType()!=Mage_Catalog_Model_Product_Type::TYPE_GROUPED &&
					$this->getType()!=Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){			
				if(trim($this->getManageStock())==1){
					$qty=trim($this->getQty());
					if (!Zend_Validate::is( $qty, 'NotEmpty')) {
						$errors[] = $helper->__('The Product Stock cannot be empty');
					}
					else if(!is_numeric($qty))
						$errors[] = $helper->__('The Product Stock must be a valid Number');
				}
			}
			
			if($this->getType()!=Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && $this->getType()!=Mage_Catalog_Model_Product_Type::TYPE_GROUPED){					
				if (!Zend_Validate::is( trim($this->getTaxClassId()) , 'NotEmpty')  && !in_array('tax_class_id',$use_default)) {
					$errors[] = $helper->__('The Product Tax Class cannot be empty');
				}
				
				$price=trim($this->getPrice());
				if (!Zend_Validate::is( $price, 'NotEmpty')  && !in_array('price',$use_default)) {
					$errors[] = $helper->__('The Product Price cannot be empty');
				}
				else if(!is_numeric($price)&&!($price>0)  && !in_array('price',$use_default))
					$errors[] = $helper->__('The Product Price must be 0 or Greater');
				
				$special_price=trim($this->getSpecialPrice());
				if($special_price!=''){
					if(!is_numeric($special_price)&&!($special_price>0) && !in_array('special_price',$use_default))
						$errors[] = $helper->__('The Product Special Price must be 0 or Greater');
				}
			}
			if (!count($errors)) {
				return true;
			}
			return $errors;		}		else		{			parent::validate($product);		}
	}
	
	/**
	 * Save Product
	 * @params $mode
	 * @return int product id
	 */
	public function saveProduct($mode) {			if(Mage::getStoreConfig('ced_vproducts/general/activation',Mage::app()->getStore()->getId())){
			$product=array();
			if(Mage::registry('saved_product')!=null)
				$product=Mage::registry('saved_product');
			$productData=Mage::app()->getRequest()->getPost() ;
			$productId=$product->getId();
			
			/**
			 * Relate Product data
			 * @params int mode,int $productId,array $productData
			 */
			$vproductModel=$this->processPostSave($mode,$product,$productData);					if(Mage::helper('csproduct')->isMobile()){				$pro_id = $vproductModel->getProductId();				$pro = Mage::getModel('catalog/product')->load($pro_id);				Mage::helper('csmarketplace/vproducts_image')->saveImages ($pro, $productData);			}
			/**
			 * Send Product Mails
			 * @params array productid,int $status
			 */
			if(!$this->isProductApprovalRequired() && $mode==self::NEW_PRODUCT_MODE){
				Mage::helper('csmarketplace/mail')
				->sendProductNotificationEmail(array($productId),Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS);
			}
			return $vproductModel->getId();		}		else		{			parent::saveProduct($mode);		}
		
	}
	
	
	/**
	 * Relate Product Data
	 * @params $mode,int $productId,array $productData
	 * 
	 */
	public function processPostSave($mode,$product,$productData){
		$websiteIds='';
		if(isset($productData['product']['website_ids']))
			$websiteIds=implode(",",$productData['product']['website_ids']);
		else 
			$websiteIds=implode(",",$product->getWebsiteIds());
		$productId=$product->getId();
		$storeId=$this->getStoreId();
		switch($mode) {
			case self::NEW_PRODUCT_MODE:
										$vproductsModel=Mage::getModel('csmarketplace/vproducts');
										$vproductsModel
										->setData ( isset($productData['product'])?$productData['product']:'' )
										->setQty(isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:'')
										->setIsInStock(isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:'')
										->setPrice($product->getPrice())
										->setSpecialPrice($product->getSpecialPrice())
										->setCheckStatus ($this->isProductApprovalRequired()?self::PENDING_STATUS:self::APPROVED_STATUS )
										->setProductId ($productId)
										->setVendorId(Mage::getSingleton('customer/session')->getVendorId())
										->setType($product->getTypeId())
										->setWebsiteIds($websiteIds)
										->setStatus($this->isProductApprovalRequired()?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
										return $vproductsModel->save ();

			case self::EDIT_PRODUCT_MODE:
									$model=$this->loadByField(array('product_id'),array($product->getId()));
									if($model && $model->getId()){
										if(isset($productData['product']['status']) && $model->getCheckStatus()!=Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS){
											unset($productData['product']['status']);
										}
										$model->addData ( isset($productData['product'])?$productData['product']:array() );
										$model->addData ( isset($productData['product']['stock_data'])?$productData['product']['stock_data']:array() );
										$model->addData(array('store_id'=>$storeId,'website_ids'=>$websiteIds,'price'=>$product->getPrice(),'special_price'=>$product->getSpecialPrice()));
										if($model->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
											$model->setStatus(isset($productData['product']['status'])?$productData['product']['status']:Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
										$this->extractNonEditableData($model);
										return $model->save ();
									}
		}
	}
	
	/**
	 * get Allowed WebsiteIds
	 * @return array websiteIds
	 */
	public function getAllowedWebsiteIds(){
			$webisteIds=Mage::getModel('csmarketplace/vendor')->getWebsiteIds(Mage::getSingleton('customer/session')->getVendorId());
			return $webisteIds;
	}
	
	/**
	 * get Current vendor Product Ids
	 * @return array $productIds
	 */
	public function getVendorProductIds($vendorId=0, $checkstatus = ''){
				
		if(!empty($this->_vproducts)){
			return $this->_vproducts;
		}else {
			$vendorId=$vendorId?$vendorId:Mage::getSingleton('customer/session')->getVendorId();
	    	$vcollection = $this->getVendorProducts('',$vendorId,0);
	    	$productids=array();
	    	if(count($vcollection)>0){
	    		foreach($vcollection as $data){
	    			array_push($productids,$data->getProductId());
	    		}
	    		$this->_vproducts=$productids;
	    	}
		}
    	return $this->_vproducts;
	}
	
	public function unsetInvalidData($data,$type=null){
		$vproducts=$this->getVendorProductIds();
		if($type!=null){
			foreach($data as $option => $optionvalue){
				foreach ($optionvalue as $key => $value){
					if(!in_array($value['product_id'],$vproducts))
						unset($data[$key]);
				}
			}
		}else{
			foreach($data as $key=>$value){
				if(!in_array($key,$vproducts))
					unset($data[$key]);
			}
		}
		return $data;
	}
	
	
	/**
	 * Get products count in category
	 * @param unknown_type $category
	 * @return unknown
	 */
	public function getProductCount($categoryId,$area='')
	{
		$vproducts=$this->getVendorProductIds();
		$resource=Mage::getSingleton('core/resource');
		$productTable =$resource->getTableName('catalog/category_product');
		$readConnection = $resource->getConnection('core_read');
		$select = $readConnection->select();
		$select->from(
				array('main_table'=>$productTable),
				array(new Zend_Db_Expr('COUNT(main_table.product_id)'))
		)
		->where('main_table.category_id = ?', $categoryId)
		->where('main_table.product_id in (?)',$vproducts)
		->group('main_table.category_id');
		$counts =$readConnection->fetchOne($select);
	
		return intval($counts);
	}
	
	/**
	 * Authenticate vendor-products association
	 * @param int $vendorId,int $productId
	 * @return boolean
	 */
	public function isAssociatedProduct($vendorId = 0, $productId = 0) {	
		if(!$vendorId || !$productId) 
			return false;
		$vproducts=$this->getVendorProductIds($vendorId);
		if(in_array($productId,$vproducts))
			return true;
		return false;
	}
	
	public function isApproved($productId = 0){
		if($productId){
			$model=$this->loadByField(array('product_id'),array($productId));
			if($model && $model->getId()){
				if($model->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
					return true;
				else 
					return false;
			}
		}
		else 
			return false;
	}
	
}

?>