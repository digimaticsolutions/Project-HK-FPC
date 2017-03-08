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
 class Ced_CsStorePickup_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	
	protected $_massactionBlockName = 'csstorepickup/widget_grid_massaction';
	/**
	 * 
	 * setting grid parameter
	 * 
	 */
	public function __construct()
    {
        parent::__construct();
        $this->setId('storepickupGrid');
        $this->setDefaultSort('pickup_id');
        $this->setTemplate('csstorepickup/widget/grid.phtml');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
		//$this->setVarNameFilter('cms');
    }

    /**
     * 
     * Preapre Collection
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
    	$vid = Mage::getSingleton('customer/session')->getVendor()->getId();
    	
        $collection = Mage::getModel('storepickup/storepickup')->getCollection()->addFieldToFilter('vendor_id',array('in'=>$vid));
       // print_r($collection->getData());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * 
     * Prepare column for grid
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
    protected function _prepareColumns()
    {//die('kjkkkkkk');
		
		$this->addColumn(
				'pickup_id',
				[
				'header' => __('ID'),
				'type' => 'number',
				'index' => 'pickup_id',
				'header_css_class' => 'col-id',
				'column_css_class' => 'col-id'
				]
		);
		
		$this->addColumn(
				'store_name',
				[
				'header' => __('Store Name'),
				'index' => 'store_name',
				'class' => 'xxx'
				]
		);
		
		$this->addColumn(
				'store_manager_name',
				[
				'header' => __('Store Manager Name'),
				'index' => 'store_manager_name',
				'class' => 'xxx'
				]
		);
		
		$this->addColumn(
				'store_manager_email',
				[
				'header' => __('Store Manager Email'),
				'index' => 'store_manager_email',
				'class' => 'xxx'
				]
		);
		
		 $this->addColumn('is_approved', array(
            'header'    => Mage::helper('catalog')->__('Approval Status'),
            'index'     => 'is_approved',
            'type'      => 'options',
            'options'   => Mage::getSingleton('csstorepickup/status')->getAvailableStatuses()
        ));

		$this->addColumn(
				'is_active',
				[
				'header' => __('Status'),
				'index' => 'is_active',
				'type' => 'options',
				'options' => ['1' => __('Enable'), '0' => __('Disable')],
				]
		);
		
		$this->addColumn(
				'edit',
				[
				'header' => __('Edit'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
				[
				'caption' => __('Edit'),
				'url' => [
				'base' => '*/*/edit'
				],
				'field' => 'pickup_id'
				]
				],
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action'
				]
		);
		
		return parent::_prepareColumns();
		
	}
    
    /**
     * prepare mass action
     * 
     * */
protected function _prepareMassaction()
    {
    	$this->setMassactionIdField('pickup_id');
    	$this->getMassactionBlock()->setFormFieldName('pickup_id');
    
    	$this->getMassactionBlock()->addItem('delete', array(
    			'label'=> Mage::helper('catalog')->__('Delete'),
    			'url'  => Mage::getUrl('*/*/delete',array(
    			'confirm' => Mage::helper('catalog')->__('Are you sure?')
    			))
    	));
    	$this->getMassactionBlock()->addItem('enable', array(
    			'label'=> Mage::helper('catalog')->__('Enable'),
    			'url'  => Mage::getUrl('*/*/enable')
    	));
    	$this->getMassactionBlock()->addItem('disable', array(
    			'label'=> Mage::helper('catalog')->__('Disable'),
    			'url'  => Mage::getUrl('*/*/disable')
    	));
    
         return $this;
    }

    /**
     * 
     * Loding Collection
     * @see Mage_Adminhtml_Block_Widget_Grid::_afterLoadCollection()
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * 
     * 
     * @param  $collection
     * @param  $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

	/**
     * Row click url
     *
     * @return string
     */
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid');
	}
    
	/**
	 * getting row url
	 *
	 * @return string
	 */
    public function getRowUrl($row)
    {
    	return Mage::getUrl('*/*/edit', array('page_id' => $row->getPageId()));
    }
	}

