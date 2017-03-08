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
 class Ced_CsStorePickup_Block_Adminhtml_Stores_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
    public function __construct()
    {
    	
        parent::__construct();
        $this->setId('storepickupGrid');
        $this->setDefaultSort('pickup_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
    	
        $collection = Mage::getModel('storepickup/storepickup')->getCollection()->addFieldToFilter('vendor_id',array('nin'=>0));
       // print_r($collection->getData());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareMassaction()
    {
    	$status = array (
    			'Approve' => 'Approve',
    			'Disapprove' => 'Disapprove',
    			
   
    	);
    	$this->setMassactionIdField('pickup_id');
    	$this->getMassactionBlock()->setFormFieldName('pickup_id');
    
    	$this->getMassactionBlock()->addItem('delete', array(
    			'label'=> Mage::helper('catalog')->__('Delete'),
    			'url'  => Mage::getUrl('*/*/delete',array(
    			'confirm' => Mage::helper('catalog')->__('Are you sure?')
    			))
    	))->addItem ( 'status', array (
        'label' => Mage::helper ( 'catalog' )->__ ( 'Change Status' ),
        'url' => $this->getUrl ( '*/*/massstatus', array (
            '_current' => true 
        ) ),
        'additional' => array (
            'status' => array (
                'name' => 'status',
                'type' => 'select',
                'class' => 'required-entry',
                'label' => Mage::helper ( 'catalog' )->__ ( 'status' ),
                'values' => $status 
            ) 
        ) 
    ) );
         return $this;
    }
    
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
		
		$this->addColumn(
				'is_active',
				[
				'header' => __('Status'),
				'index' => 'is_active',
				'type' => 'options',
				'options' => ['1' => __('Enable'), '0' => __('Disable')],
				]
		);
		
		$this->addColumn('is_approved', array(
				'header'    => Mage::helper('catalog')->__('Approval Status'),
				'index'     => 'is_approved',
				'type'      => 'options',
				'options'   => Mage::getSingleton('csstorepickup/status')->getAvailableStatuses()
		));
		
		$this->addColumn('approve', array(
				'header'        => Mage::helper('catalog')->__('Action'),
				'align'     	=> 'left',
				'index'         => 'block_id',
				'filter'   	 	=> false,
				'sortable'  	=> false,
				'type'          => 'text',
				'renderer' => 'Ced_CsStorePickup_Block_Adminhtml_Grid_Renderer_Approve',
		));
		
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
    public function getRowUrl($row)
    {
    	return $this->getUrl('*/*/edit', array('pickup_id' => $row->getId()));
    }
  }

