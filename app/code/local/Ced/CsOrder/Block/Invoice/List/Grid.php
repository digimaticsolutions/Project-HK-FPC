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
class Ced_CsOrder_Block_Invoice_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('csorder_invoice_grid');
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
        return 'sales/order_invoice_grid_collection';
    }

    protected function _prepareCollection()
    {
    	$collection = Mage::getResourceModel($this->_getCollectionClass());
		
		$vendor_id = Mage::getSingleton('customer/session')->getVendorId();
		$table = Mage::getSingleton('core/resource')->getTableName('csorder/invoice');
		$collection->getSelect()->join( array('invoice_flat'=> $table), 'invoice_flat.invoice_id = main_table.entity_id', array('invoice_flat.vendor_id'))->where("vendor_id = ".$vendor_id);
		


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
       
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('csorder')->__('Invoice #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('csorder')->__('Invoice Date'),
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

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('csorder')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('csorder')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Amount'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
			'renderer'  => 'csorder/adminhtml_sales_order_invoice_renderer_grandtotal'
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
                        'url'     => array('base'=>'*/*/view', 'params' => array("_nosecret" => true, 'come_from' => 'invoice')),
                        'field'   => 'invoice_id'
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
        $this->getMassactionBlock()->setFormFieldName('invoice_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('PDF Invoices'),
             'url'  => $this->getUrl('*/*/pdfinvoices',array('_secure'=>true)),
        ));

        return $this;
    }

   

  
	/**
     * Row click url
     *
     * @return string
     */
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid',array('_secure'=>true));
	}
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view',
            array(
                'invoice_id'=> $row->getId(),
				'come_from' => 'invoice',
				'_secure'=>true
            )
        );
    }
	
	/**
     * Enter description here...
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }
}
