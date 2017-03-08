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

class Ced_CsCmsPage_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
	 * 
	 */
	public function __construct()
    {
        parent::__construct();
        $this->setId('cmsPageGrid');
        $this->setDefaultSort('page_id');
        $this->setTemplate('cscmspage/widget/grid.phtml');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
		$this->setVarNameFilter('cms');
    }

    /**
     * 
     * Preapre Collection
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
    	$VendorId = Mage::getSingleton('customer/session')->getVendorId();
        $collection = Mage::getModel('cscmspage/cmspage')->getCollection();
		$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
		$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
		$collection = $collection->addFieldToFilter("$main_table.$vendor_id",array('eq'=>$VendorId));
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * 
     * Prepare column for grid
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('title', array(
            'header'    => Mage::helper('cscmspage')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('cscmspage')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));

		$this->addColumn('cmspageurl', array(
            'header'    => Mage::helper('cscmspage')->__(' CMS Url'),
            'align'     => 'left',
        	'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Ced_CsCmsPage_Block_Adminhtml_Cmspage_Grid_Renderer_Vendorcmsurl',
        ));
        

        $this->addColumn('root_template', array(
            'header'    => Mage::helper('cms')->__('Layout'),
            'index'     => 'root_template',
            'type'      => 'options',
            'options'   => Mage::getSingleton('page/source_layout')->getOptions(),
        ));

        /**
         * Check is single store mode
         */
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
            'options'   => Mage::getSingleton('cscmspage/vendorcms')->getVendorCmsStatus()
        ));
        
        $this->addColumn('is_approve', array(
            'header'    => Mage::helper('cscmspage')->__('Approval Status'),
            'index'     => 'is_approve',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cscmspage/vendorcms')->getAvailableStatuses()
        ));
        
        /*$this->addColumn('is_home', array(
            'header'    => Mage::helper('cscmspage')->__('Home Page'),
            'index'     => 'is_approve',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cscmspage/vendorcms')->getAvailableStatuses()
        )); */

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

        $this->addColumn('page_actions', array(
            'header'    => Mage::helper('cscmspage')->__('Action'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Ced_CsCmsPage_Block_Adminhtml_Cmspage_Grid_Renderer_Action',
        ));
		return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action
     * 
     * */
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('page_id');
        $this->getMassactionBlock()->setFormFieldName('page_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('cscmspage')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('cscmspage')->__('Are you sure?')
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
