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
 * @category    Ced;
 * @package     Ced_CsOrder 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsOrder_Block_Shipment_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	protected $_massactionBlockName = 'csorder/shipment_widget_grid_massaction';

	public function __construct()
    { 
        parent::__construct();
		$this->setTemplate('csmarketplace/widget/grid.phtml');
        $this->setId('csorder_shipment_grid');
        $this->setDefaultSort('created_at');
		$this->setUseAjax(true);
        $this->setDefaultDir('DESC');
    }
	
	 /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_shipment_grid_collection';
    }

    protected function _prepareCollection()
    {
    	$collection = Mage::getResourceModel($this->_getCollectionClass());
		
		$vendor_id = Mage::getSingleton('customer/session')->getVendorId();
		$table = Mage::getSingleton('core/resource')->getTableName('csorder/shipment');
		$collection->getSelect()->join( array('shipment_flat'=> $table), 'shipment_flat.shipment_id = main_table.entity_id', array('shipment_flat.vendor_id'))->where("vendor_id = ".$vendor_id);


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

	/**
	 * Prepare Columns
	 *
	 * @return object
	 */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();


        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('csorder')->__('Shipment #'),
            'type'      => 'text',
            'index'     => 'increment_id',
        ));


        $this->addColumn('created_at', array(
            'header'    => Mage::helper('csorder')->__('Date Shipped'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('csorder')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('csorder')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('csorder')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('csorder')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('csorder')->__('View'),
                        'url'     => array('base'=>'*/*/view',  'params' => array("_nosecret" => true, 'come_from' => 'shipment')),
                        'field'   => 'shipment_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('csorder')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('csorder')->__('Excel XML'));
		return parent::_prepareColumns();
    }
    
	/**
	 * Prepare Mass Action
	 *
	 * @return object
	 */
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('csorder')->__('PDF Packingslips'),
             'url'  => $this->getUrl('*/*/pdfshipments',array('_secure'=>true)),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('csorder')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/*/massPrintShippingLabel',array('_secure'=>true)),
        ));

        return $this;
    }

   

  
	/**
     * Get Grid url
     *
     * @return string
     */
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid');
	}
    
	/**
	 * Get Row url
	 *
	 * @return string
	 */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view',
            array(
                'shipment_id'=> $row->getId(),
				'come_from' => 'shipment',
				'_secure'=>true
            )
        );
    }
	/**
     * get model class identifier
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }
}
