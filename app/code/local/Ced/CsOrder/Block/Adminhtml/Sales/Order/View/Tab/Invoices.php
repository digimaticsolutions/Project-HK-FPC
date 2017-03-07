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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_View_Tab_Invoices
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
	{
	   public function __construct()
		{ 
			parent::__construct();
			$this->setId('order_invoices');
			$this->setUseAjax(true);
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

	/**
	 * Prepare collection
	 *
	 * @return object
	 */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('state')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('base_grand_total')
            ->addFieldToSelect('store_currency_code')
            ->addFieldToSelect('base_currency_code')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('billing_name')
            ->setOrderFilter($this->getOrder())
        ;
		
		$vendor_id = Mage::getSingleton('customer/session')->getVendorId();
		$table = Mage::getSingleton('core/resource')->getTableName('csorder/invoice');
		$collection->getSelect()->join( array('invoice_flat'=> $table), 'invoice_flat.invoice_id = main_table.entity_id', array('invoice_flat.vendor_id'))->where("vendor_id = ".$vendor_id);
		
		
        $this->setCollection($collection);
		return $collection;
        return parent::_prepareCollection();
    }

	/**
	 * prepare columns 
	 *
	 * @return object
	 */
	protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Invoice #'),
            'index'     => 'increment_id',
            'width'     => '120px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Invoice Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));


		if($this->getVorder()->isAdvanceOrder()){
			$this->addColumn('base_grand_total', array(
				'header'    => Mage::helper('customer')->__('Amount'),
				'index'     => 'base_grand_total',
				'renderer'  => 'csorder/adminhtml_sales_order_invoice_renderer_grandtotal'
			));
		}else{
			$this->addColumn('base_grand_total', array(
				'header'    => Mage::helper('customer')->__('Amount'),
				'index'     => 'base_grand_total',
				'renderer'  => 'csorder/adminhtml_sales_order_invoice_renderer_grandtotal',
				'filter'	=> false,
				
			));

		}

        return $this;
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
     * Retrieve order model instance
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVorder()
    {
        return Mage::registry('current_vorder');
    }
	
	
	 public function getRowUrl($row)
    {
        return $this->getUrl('*/invoice/view',
            array(
                'invoice_id'=> $row->getId(),
                'order_id'  => $row->getOrderId(),
				'_secure'=>true
            )
        );
    }
	
	 public function getGridUrl()
    {
        return $this->getUrl('*/*/invoices', array('_current' => true,'_secure'=>true));
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
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Invoices');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order Invoices');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
