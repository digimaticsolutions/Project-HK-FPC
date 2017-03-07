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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_View_Tab_Shipments
     extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_shipments');
        $this->setUseAjax(true);
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


	/**
	 * Prepare Collection
	 *
	 * @return object
	 */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('total_qty')
            ->addFieldToSelect('shipping_name')
            ->setOrderFilter($this->getOrder())
        ;
		
		
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
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Shipment #'),
            'index' => 'increment_id',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Date Shipped'),
            'index' => 'created_at',
            'type' => 'datetime',
        ));


	if($this->getVorder()->isAdvanceOrder()){
        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));
	}else{
		$this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
			'renderer'  => 'csorder/adminhtml_sales_order_shipment_renderer_totalqty',
			'filter'	=> false,
        ));
	}
        return parent::_prepareColumns();
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }


  

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Shipments');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order Shipments');
    }

    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }


    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/shipment/view',
            array(
                'shipment_id'=> $row->getId(),
                'order_id'  => $row->getOrderId(),
				'_secure'=>true
             ));
    }
	
	 public function getGridUrl()
    {
        return $this->getUrl('*/*/shipments', array('_current' => true,'_secure'=>true));
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

    /**
     * Retrieve order model instance
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVorder()
    {
        return Mage::registry('current_vorder');
    }
	
}
