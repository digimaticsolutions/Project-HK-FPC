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
class Ced_CsOrder_Block_Vorders_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Constructor
	 *
	 */
    public function __construct()
    {
        parent::__construct();
		$this->setTemplate('csmarketplace/widget/grid.phtml');
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		
		$vendor =  Mage::getSingleton('customer/session')->getVendor();
		$collection = $vendor->getAssociatedOrders();
		$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
		$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
		$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
		$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
		
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From <br />(Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

       

        $this->addColumn('order_total', array(
            'header' => Mage::helper('sales')->__('G.T.'),
            'index' => 'order_total',
            'type'  => 'currency',
            'currency' => 'currency',
        ));
		
        $this->addColumn('shop_commission_fee', array(
            'header' => Mage::helper('sales')->__('Commission Fee'),
            'index' => 'shop_commission_fee',
            'type'  => 'currency',
            'currency' => 'currency',
        ));
		
		$this->addColumn('net_vendor_earn', array(
				'header' => Mage::helper('sales')->__('Net Earned'),
				'index' => 'net_vendor_earn',
				'type'  => 'currency',
				'currency' => 'currency',
			));
			
		$this->addColumn('order_payment_state', array(
				'header' => Mage::helper('sales')->__('Order Payment<br /> Status'),
				'index' => 'order_payment_state',
				'type'  => 'options',
				'options' => Mage::getModel('sales/order_invoice')->getStates(),
			));
			
		$this->addColumn('payment_state', array(
			'header' => Mage::helper('sales')->__('Vendor Payment <br /> Status'),
			'index' => 'payment_state',
			'type'  => 'options',
			'options' => Mage::getModel('csmarketplace/vorders')->getStates(),
		));
			
       
		  // $link= $this->getUrl('csorder/vorders/view');
	   
	   
	   				

            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
					
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'csorder/vorders/view' , 'params' => array("_nosecret" => true)),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));

        $this->addExportType('csorder/vorders/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('csorder/vorders/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('csorder/vorders/view', array('order_id' => $row->getId(),'_secure'=>true));
    }

    public function getGridUrl()
    {
        return $this->getUrl('csorder/vorders/grid', array('_current'=>true,'_secure'=>true));
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
