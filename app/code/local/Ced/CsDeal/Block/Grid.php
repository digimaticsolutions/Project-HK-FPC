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
 * @package     Ced_CsProduct
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage products grid block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsDeal_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	protected $_massactionBlockName = 'csmarketplace/widget_grid_massaction';

	public function __construct()
	{
		
		parent::__construct();
		$this->setId('dealproductGrid');
		$this->setDefaultSort('entity_id');
		$this->setTemplate('csmarketplace/widget/grid.phtml');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('product_filter');
		
	}
	
	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
	
		
	protected function _prepareCollection()
	{
		$vproducts = array();
		$vendorId=Mage::getModel('customer/session')->getVendorId();
		$vproducts = Mage::getModel('csmarketplace/vproducts')->getVendorProductIds($vendorId);
		$dealproducts=Mage::getModel('csdeal/deal')->getVendorDealProductIds($vendorId);
		$store = $this->_getStore();
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection = $collection->addAttributeToSelect('sku')
		->addAttributeToSelect('name')
		->addAttributeToSelect('attribute_set_id')
		->addAttributeToSelect('type_id');
		if(count($vproducts))
		$collection->addFieldToFilter('entity_id',array('in'=>$vproducts));
		if(count($dealproducts))
		$collection->addFieldToFilter('entity_id',array('nin'=>$dealproducts));
		//$collection->addAttributeToFilter('type_id', array('eq' => array('simple')));
		//$collection->addAttributeToFilter('type_id', array('eq' => array('virtual')));
		$collection->addAttributeToFilter('visibility' , array('nin'=>array('0'=> Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)));
		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
			$collection->joinField('qty',
					'cataloginventory/stock_item',
					'qty',
					'product_id=entity_id',
					'{{table}}.stock_id=1',
					'left');
		}
		
		if ($store->getId()) {
			$collection->addStoreFilter($store);
			$collection->joinAttribute(
					'name',
					'catalog_product/name',
					'entity_id',
					null,
					'inner',
					$store->getId()
			);
			$collection->joinAttribute(
					'status',
					'catalog_product/status',
					'entity_id',
					null,
					'inner',
					$store->getId()
			);
			$collection->joinAttribute(
					'visibility',
					'catalog_product/visibility',
					'entity_id',
					null,
					'inner',
					$store->getId()
			);
			$collection->joinAttribute(
					'price',
					'catalog_product/price',
					'entity_id',
					null,
					'left',
					$store->getId()
			);
		}
		else {
			$collection->addAttributeToSelect('price');
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
		}
		
		$collection->joinField('check_status','csmarketplace/vproducts', 'check_status','product_id=entity_id',null,'left');
		
		
		$this->setCollection($collection);
		parent::_prepareCollection();
		$this->getCollection()->addWebsiteNamesToResult();
		return $this;
	}
	
	protected function _prepareColumns()
	{
		$store = $this->_getStore();
		$this->addColumn('entity_id',
				array(
						'header'=> Mage::helper('catalog')->__('Product ID'),
						'width' => '5px',
						'type'  => 'number',
						'align'     => 'left',
						'index' => 'entity_id',
						'renderer'=>'Ced_CsDeal_Block_Grid_Renderer_ProductId'
				));
		$this->addColumn('name',
				array(
						'header'=> Mage::helper('catalog')->__('Name'),
						'index' => 'name',
						'renderer' => 'Ced_CsDeal_Block_Grid_Renderer_ProductImage',
				));
		 
		$this->addColumn('type_id',
				array(
						'header'=> Mage::helper('catalog')->__('Type'),
						'width' => '10px',
						'index' => 'type_id',
						'type'  => 'options',
						'options' => Mage::getSingleton('csmarketplace/system_config_source_vproducts_type')->toFilterOptionArray(true,false,$store->getId()),
				));
	
		$this->addColumn('price',
				array(
						'header'=> Mage::helper('catalog')->__('Price'),
						'type'  => 'price',
						'currency_code' => $store->getBaseCurrency()->getCode(),
						'index' => 'price',
				));
	
		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
			$this->addColumn('qty',
					array(
							'header'=> Mage::helper('catalog')->__('Qty'),
							'width' => '50px',
							'type'  => 'number',
							'index' => 'qty',
					));
		}
		$this->addColumn('check_status',
				array(
						'header'=> Mage::helper('catalog')->__('Status'),
						'width' => '70px',
						'index' => 'check_status',
						'type'  => 'options',
						'renderer'=>'Ced_CsDeal_Block_Grid_Renderer_ProductStatus',
						'options' => Mage::getSingleton('csmarketplace/vproducts')->getVendorOptionArray(),
				));

		$this->addColumn('create_deal',
				array(
						'header'=> Mage::helper('catalog')->__('Create'),
						'width' => '70px',
						'index' => 'create_deal',
						'sortable' => false,
        				'filter'   => false,
						'renderer'=>'Ced_CsDeal_Block_Grid_Renderer_Deal',
						));
		//$this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
	
		return parent::_prepareColumns();
	}
	
	
	protected function _productNameFilter($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}
		$this->getCollection()
		->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'));
		 
		return $this;
	}
	
	protected function _productStatusFilter($collection, $column){
		if(!strlen($column->getFilter()->getValue())) {
			return $this;
		}
		if($column->getFilter()->getValue()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS.Mage_Catalog_Model_Product_Status::STATUS_ENABLED){
			$this->getCollection()
			->addAttributeToFilter('check_status', array('eq' =>Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS))
			->addAttributeToFilter('status', array('eq' =>Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
		}
		else if($column->getFilter()->getValue()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS.Mage_Catalog_Model_Product_Status::STATUS_DISABLED){
			$this->getCollection()
			->addAttributeToFilter('check_status', array('eq' =>Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS))
			->addAttributeToFilter('status', array('eq' =>Mage_Catalog_Model_Product_Status::STATUS_DISABLED));
		}
		else 
			$this->getCollection()->addAttributeToFilter('check_status', array('eq' =>$column->getFilter()->getValue()));
		return $this;
	}
	
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/creategrid',array(
				'store'=>$this->getRequest()->getParam('store')));
	}
}
