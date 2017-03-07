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

/**
 * Manage Attributes grid block
 *
 * @category   Ced
 * @package    Ced_CsVendorProductAttribute
 */
class Ced_CsVendorProductAttribute_Block_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   /**
    * Set a Default Values In constructor
    
    */
	public function __construct()
	{
	    parent::__construct();
        $this->setId('attributeGrid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }
	
	/**
	 * Prepare vendor product attributes grid collection object
	 *
	 * @return Ced_CsVendorProductAttribute_Block_Attribute_Grid
	 */
	protected function _prepareCollection()
	{
		
		$vendor_id=Mage::getSingleton('customer/session')->getVendorId();
		$table = "ced_vendor_product_attributes";
  		$tableName = Mage::getSingleton("core/resource")->getTableName($table);
 		$collection = Mage::getResourceModel('catalog/product_attribute_collection')
  					  ->addVisibleFilter();
  		$collection->getSelect()
  					->join($tableName,'main_table.attribute_id = '.$tableName.'.attribute_id', array('*'=> '*'));
		$collection->addFieldToFilter('vendor_id',$vendor_id);
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * Prepare vendor product attributes grid columns
	 *
	 * @return Ced_CsVendorProductAttribute_Block_Attribute_Grid
	 */
   protected function _prepareColumns()
	{
		parent::_prepareColumns();
		$this->addColumn('attribute_code', array(
				'header'=>Mage::helper('eav')->__('Attribute Code'),
				'sortable'=>true,
				'index'=>'attribute_code',
				'filter_index'=>'main_table.attribute_code'
		));
		
		$this->addColumn('frontend_label', array(
				'header'=>Mage::helper('eav')->__('Attribute Label'),
				'sortable'=>true,
				'index'=>'frontend_label'
		));
		
		$this->addColumn('is_required', array(
				'header'=>Mage::helper('eav')->__('Required'),
				'sortable'=>true,
				'index'=>'is_required',
				'type' => 'options',
				'options' => array(
						'1' => Mage::helper('eav')->__('Yes'),
						'0' => Mage::helper('eav')->__('No'),
				),
				'align' => 'center',
		));
		
		$this->addColumn('is_user_defined', array(
				'header'=>Mage::helper('eav')->__('System'),
				'sortable'=>true,
				'index'=>'is_user_defined',
				'type' => 'options',
				'align' => 'center',
				'options' => array(
						'0' => Mage::helper('eav')->__('Yes'),   // intended reverted use
						'1' => Mage::helper('eav')->__('No'),    // intended reverted use
				),
		));
		
		  $this->addColumnAfter('is_visible', array(
            'header'=>Mage::helper('catalog')->__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumnAfter('is_global', array(
            'header'=>Mage::helper('catalog')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
            ),
            'align' => 'center',
        ), 'is_visible');

        $this->addColumn('is_searchable', array(
            'header'=>Mage::helper('catalog')->__('Searchable'),
            'sortable'=>true,
            'index'=>'is_searchable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_user_defined');

        $this->addColumnAfter('is_filterable', array(
            'header'=>Mage::helper('catalog')->__('Use in Layered Navigation'),
            'sortable'=>true,
            'index'=>'is_filterable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Filterable (with results)'),
                '2' => Mage::helper('catalog')->__('Filterable (no results)'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_searchable');

        $this->addColumnAfter('is_comparable', array(
            'header'=>Mage::helper('catalog')->__('Comparable'),
            'sortable'=>true,
            'index'=>'is_comparable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_filterable');
		
	    return $this;
	}

	
	public function getRowUrl($row)
	{
		return Mage::getUrl('csvendorproductattribute/attribute/edit', array(
				'store'=>$this->getRequest()->getParam('store'),
				'attribute_id'=>$row->getId())
		);
	}
	public function getGridUrl() {
		return $this->getUrl('csvendorproductattribute/attribute/grid', array('_secure'=>true, '_current'=>true));
	}
}
