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
class Ced_CsCmsPage_Block_Adminhtml_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	/**
	 * 
	 * setting grid parameter
	 * @return void
	 */
public function __construct()
    {
        parent::__construct();
        $this->setId('cmsPageGrid');
        $this->setDefaultSort('block_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * 
     * Preparing collection
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cscmspage/block')->getCollection();
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * 
     * Preparing columns
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
            'header'    => Mage::helper('cscmspage')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));
      	
        $VendorCollection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*');
		$AllVendors = array();
        if(sizeof($VendorCollection)>0){
	        foreach($VendorCollection as $vendor){
	        	$AllVendors[$vendor->getEntityId()] = $vendor->getName();
	        }
        }
        $this->addColumn('vendor_id', array(
            'header'    => Mage::helper('cscmspage')->__('Vendor Name'),
            'width' => '10px',
            'index' => 'vendor_id',
            'type'  => 'options',
            'sortable' => false,
            'options' => $AllVendors,
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
            'options'   => array(
                0 => Mage::helper('cscmspage')->__('Disabled'),
                1 => Mage::helper('cscmspage')->__('Enabled')
            ),
        ));
        
        $this->addColumn('is_approve', array(
            'header'    => Mage::helper('cscmspage')->__('Approval Status'),
            'index'     => 'is_approve',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cscmspage/vendorcms')->getAvailableStatuses()
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
        
		$this->addColumn('approve', array(
					'header'        => Mage::helper('cscmspage')->__('Action'),
					'align'     	=> 'left',
					'index'         => 'block_id',
					'filter'   	 	=> false,
					'sortable'  	=> false,
					'type'          => 'text',
					'renderer' => 'Ced_CsCmsPage_Block_Adminhtml_Block_Entity_Grid_Renderer_Approve',
					));
				
        return parent::_prepareColumns();
    }
       
    /**
     * 
     * prepare massaction here
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

        
         $this->getMassactionBlock()->addItem('approve', array(
             'label'=> Mage::helper('cscmspage')->__('Approve'),
             'url'  => $this->getUrl('*/*/massApprove'),
             'confirm' => Mage::helper('cscmspage')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('disapproved', array(
             'label'=> Mage::helper('cscmspage')->__('Disapprove'),
             'url'  => $this->getUrl('*/*/massdisApprove'),
             'confirm' => Mage::helper('cscmspage')->__('Are you sure?')
        ));

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    /**
     * 
     * Loading collection
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
     * 
     * Filtering Collection
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
    public function getRowUrl($row)
    {
    	return $this->getUrl('*/*/edit', array('block_id' => $row->getId()));
    }
    
}