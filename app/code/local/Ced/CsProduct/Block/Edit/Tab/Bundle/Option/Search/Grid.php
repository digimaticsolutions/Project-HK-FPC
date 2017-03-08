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
 * Bundle selection product grid
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Bundle_Option_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Set grid params
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('csmarketplace/widget/grid.phtml');
		$this->setId('bundle_selection_search_grid');
		$this->setRowClickCallback('bSelection.productGridRowClick.bind(bSelection)');
		$this->setCheckboxCheckCallback('bSelection.productGridCheckboxCheck.bind(bSelection)');
		$this->setRowInitCallback('bSelection.productGridRowInit.bind(bSelection)');
		$this->setDefaultSort('id');
		$this->setUseAjax(true);
	}
	
	protected function _beforeToHtml()
	{
		$this->setId($this->getId().'_'.$this->getIndex());
		$this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
		$this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
	
		return parent::_beforeToHtml();
	}
	
	/**
	 * Prepare collection
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
    protected function _prepareCollection()
    {
    		$vproducts=Mage::getModel('csmarketplace/vproducts')->getVendorProductIds();
    		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	    	$collection = Mage::getModel('catalog/product')->getCollection()
	            ->setStore($this->getStore())
	            ->addAttributeToSelect('name')
	            ->addAttributeToSelect('sku')
	            ->addAttributeToSelect('price')
	            ->addAttributeToSelect('attribute_set_id')
	            ->addAttributeToFilter('type_id', array('in' => $this->getAllowedSelectionTypes()))
	            ->addAttributeToFilter('entity_id',array('in'=>$vproducts))
	            ->addFilterByRequiredOptions()
	            ->addStoreFilter();
	
	        if ($products = $this->_getProducts()) {
	            $collection->addIdFilter($this->_getProducts(), true);
	        }
	
	        if ($this->getFirstShow()) {
	            $collection->addIdFilter('-1');
	            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
	        }
	
	        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
	
	        $this->setCollection($collection);
	        return parent::_prepareCollection();
       
    }
    
    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
    	$this->addColumn('id', array(
    			'header'    => Mage::helper('sales')->__('ID'),
    			'sortable'  => true,
    			'width'     => '60px',
    			'index'     => 'entity_id'
    	));
    	$this->addColumn('name', array(
    			'header'    => Mage::helper('sales')->__('Product Name'),
    			'index'     => 'name',
    			'column_css_class'=> 'name'
    	));
    
    	$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
    	->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
    	->load()
    	->toOptionHash();
    
    	$this->addColumn('set_name',
    			array(
    					'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
    					'width' => '100px',
    					'index' => 'attribute_set_id',
    					'type'  => 'options',
    					'options' => $sets,
    			));
    
    	$this->addColumn('sku', array(
    			'header'    => Mage::helper('sales')->__('SKU'),
    			'width'     => '80px',
    			'index'     => 'sku',
    			'column_css_class'=> 'sku'
    	));
    	$this->addColumn('price', array(
    			'header'    => Mage::helper('sales')->__('Price'),
    			'align'     => 'center',
    			'type'      => 'currency',
    			'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
    			'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
    			'index'     => 'price'
    	));
    
    	$this->addColumn('is_selected', array(
    			'header_css_class' => 'a-center',
    			'type'      => 'checkbox',
    			'name'      => 'in_selected',
    			'align'     => 'center',
    			'values'    => $this->_getSelectedProducts(),
    			'index'     => 'entity_id',
    	));
    
    	$this->addColumn('qty', array(
    			'filter'    => false,
    			'sortable'  => false,
    			'header'    => Mage::helper('sales')->__('Qty to Add'),
    			'name'      => 'qty',
    			'inline_css'=> 'qty',
    			'align'     => 'right',
    			'type'      => 'input',
    			'validate_class' => 'validate-number',
    			'index'     => 'qty',
    			'width'     => '130px',
    	));
    
    	return parent::_prepareColumns();
    }
    
    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
    	return $this->getUrl('*/bundle_selection/grid', array('index' => $this->getIndex(), 'productss' => implode(',', $this->_getProducts())));
    }
    
    /**
     * Retrieve selected crosssell products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
    	$products = $this->getRequest()->getPost('selected_products', array());
    	return $products;
    }
    
    protected function _getProducts()
    {
    	if ($products = $this->getRequest()->getPost('products', null)) {
    		return $products;
    	} else if ($productss = $this->getRequest()->getParam('productss', null)) {
    		return explode(',', $productss);
    	} else {
    		return array();
    	}
    }
    
    public function getStore()
    {
    	return Mage::app()->getStore();
    }
    
    /**
     * Retrieve array of allowed product types for bundle selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
    	return Mage::helper('bundle')->getAllowedSelectionTypes();
    }
}
