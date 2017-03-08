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
  * @package     Ced_CsProduct
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Manage products grid block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsProduct_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$this->setId('productGrid');
		$this->setDefaultSort('entity_id');
		$this->setTemplate('csmarketplace/widget/grid.phtml');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('product_filter');
		
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('product');
	
		$this->getMassactionBlock()->addItem('delete', array(
				'label'=> Mage::helper('catalog')->__('Delete'),
				'url'  => Mage::getUrl('*/*/massDelete',array(
				'store'=>$this->getRequest()->getParam('store'))),
				'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
	
		$statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
	
		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
				'label'=> Mage::helper('catalog')->__('Change status'),
				'url'  => Mage::getUrl('*/*/massStatus',array(
				'store'=>$this->getRequest()->getParam('store'))),
				'additional' => array(
						'visibility' => array(
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper('catalog')->__('Status'),
								'values' => $statuses
						)
				)
				
		));
	
		return $this;
	}
	
	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
	
	protected function _addColumnFilterToCollection($column)
	{
		if ($this->getCollection()) {
			if ($column->getId() == 'websites') {
				$this->getCollection()->joinField('websites',
						'catalog/product_website',
						'website_id',
						'product_id=entity_id',
						null,
						'left');
			}
		}
		return parent::_addColumnFilterToCollection($column);
	}
	
	/**
	 * Prepare collection
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareCollection()
	{
		$vproducts = array();
		$vproducts = Mage::getModel('csmarketplace/vproducts')->getVendorProductIds();
		
		$store = $this->_getStore();
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection = $collection->addAttributeToSelect('sku')
		->addAttributeToSelect('name')
		->addAttributeToSelect('attribute_set_id')
		->addAttributeToSelect('type_id')
		->addFieldToFilter('entity_id',array('in'=>$vproducts));
	
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
	
	/**
	 * Add columns to grid
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareColumns()
	{
		$store = $this->_getStore();
		$this->addColumn('entity_id',
				array(
						'header'=> Mage::helper('catalog')->__('ID'),
						'width' => '5px',
						'type'  => 'number',
						'align'     => 'left',
						'index' => 'entity_id',
						'renderer'=>'Ced_CsProduct_Block_Grid_Renderer_ProductId'
				));
		$this->addColumn('name',
				array(
						'header'=> Mage::helper('catalog')->__('Name'),
						'index' => 'name',
						'renderer' => 'Ced_CsProduct_Block_Grid_Renderer_ProductImage',
        				'filter_condition_callback' => array($this, '_productNameFilter')
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
						'renderer'=>'Ced_CsProduct_Block_Grid_Renderer_ProductStatus',
						'options' => Mage::getSingleton('csmarketplace/vproducts')->getVendorOptionArray(),
						'filter_condition_callback' => array($this, '_productStatusFilter')
				));
		
		if (!Mage::app()->isSingleStoreMode() && Mage::helper('csmarketplace')->isSharingEnabled()) {
			$this->addColumn('websites',
					array(
							'header'=> Mage::helper('catalog')->__('Websites'),
							'width' => '100px',
							'sortable'  => false,
							'index'     => 'websites',
							'type'      => 'options',
							'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
					));
		}
			
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
	
	/**
	 * Rerieve grid URL
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid',array(
				'store'=>$this->getRequest()->getParam('store')));
	}
	
	/**
	 * Rerieve row URL
	 *
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return Mage::getUrl('*/*/edit', array(
				'store'=>$this->getRequest()->getParam('store'),
				'id'=>$row->getId())
		);
	}

}
