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
  * @package     Ced_CsCmsPage
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsCmsPage_Block_Cmsblock_Blockpage_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	protected $_massactionBlockName = 'cscmspage/widget_grid_massaction';

	/**
	 * 
	 * setting grid parameter
	 * */
	public function __construct()
	{
		
		parent::__construct();
		$this->setId('cmsPageGrid');
        $this->setDefaultSort('block_id');        
		$this->setTemplate('cscmspage/widget/grid.phtml');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('block');
		
	}
	/**
	 * preparing collection and setting to grid
	 * 
	 * */
    protected function _prepareCollection()
    {
    	$VendorId = Mage::getSingleton('customer/session')->getVendorId();
    	
    	$collection = Mage::getModel('cscmspage/block')->getCollection();
		$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
		$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
		$collection = $collection->addFieldToFilter("$main_table.$vendor_id",array('eq'=>$VendorId));				
		$collection->setFirstStoreFlag(true);
        $this->setCollection($collection);
		
        return parent::_prepareCollection();
    }
    /**
     * preparing columns
     * 
     * */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('title', array(
            'header'    => Mage::helper('cscmspage')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('cscmspage')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
        	
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cscmspage')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
	
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('cscmspage')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('cscmspage')->__('Disabled'),
                1 => Mage::helper('cscmspage')->__('Enabled')
            ),
        ));
        $this->addColumn('is_approve', array(
            'header'    => Mage::helper('cscmspage')->__('Approval Status'),
            'index'     => 'is_approve',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cscmspage/vendorblock')->getAvailableStatuses()
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('cscmspage')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('cscmspage')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));
       

        return parent::_prepareColumns();
    }
    
    /**
     * preparing massaction
     * 
     * */
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('block_id');
        $this->getMassactionBlock()->setFormFieldName('block_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('cscmspage')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('cscmspage')->__('Are you sure?')
        ));

        return $this;
    }
    /**
     * 
     * Loading collection
     * @return Ced_CsCmsPage_Block_Cmsblock_Blockpage_Grid
     * 
     * */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * 
     * filtering Colection 
     * */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * getting grid url
     * 
     * */
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid');
	}
    /**
     * Row click url
     *
     * @return string
     */
	public function getRowUrl($row)
    {
    	return Mage::getUrl('*/*/edit', array('block_id' => $row->getBlockId()));
    }
}
