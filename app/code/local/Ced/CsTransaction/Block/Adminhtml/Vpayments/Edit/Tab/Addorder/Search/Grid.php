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
 * @package     Ced_CsTransaction 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsTransaction_Block_Adminhtml_Vpayments_Edit_Tab_Addorder_Search_Grid extends Ced_CsTransaction_Block_Adminhtml_Vorder_Items_Grid
{
	/**
     * Constructor
     */
	public function __construct()
    {
		
		parent::__construct();
        $this->setId('ced_csmarketplace_order_search_grid');
		$this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }


    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareCollection($flag = false)
    {
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		$vendorId = isset($params['vendor_id'])? $params['vendor_id']:0;
		$orderIds = isset($params['order_ids'])? explode(',',trim($params['order_ids'])):array();
		$orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
		if(Mage::helper('csorder')->isActive()){
			$collection = Mage::getModel('cstransaction/vorder_items')
								->getCollection()
								->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
							
			if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
				$collection->addFieldToFilter('qty_ready_to_refund',array('gt'=>0));
						   
			} else{
				$collection->addFieldToFilter('qty_ready_to_pay',array('gt'=>0));
						 
			}
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$item_fee=Mage::helper('csmarketplace')->getTableKey('item_fee');
			$qty_ready_to_pay=Mage::helper('csmarketplace')->getTableKey('qty_ready_to_pay');
			$qty_ready_to_refund=Mage::helper('csmarketplace')->getTableKey('qty_ready_to_refund');
			$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$item_fee} * {$main_table}.{$qty_ready_to_pay})")));
			$collection->getSelect()->columns(array('net_vendor_return' => new Zend_Db_Expr("({$main_table}.{$item_fee} * {$main_table}.{$qty_ready_to_refund})")));
			$collection->getSelect()->joinLeft($orderTable , 'main_table.order_increment_id ='.$orderTable.'.increment_id',array('*'));
			$collection->getSelect()->group('id');
			$this->setCollection($collection);
		}
		else
		{
			$collection = Mage::getModel('csmarketplace/vorders')->getCollection();
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$order_payment_state=Mage::helper('csmarketplace')->getTableKey('order_payment_state');
			$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
			$payment_state=Mage::helper('csmarketplace')->getTableKey('payment_state');
			$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
			$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
		
			$collection ->addFieldToFilter("{$main_table}.{$vendor_id}",array('eq'=>$vendorId));
			
			if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
				$collection->addFieldToFilter("{$main_table}.{$order_payment_state}",array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
						   ->addFieldToFilter("{$main_table}.{$payment_state}",array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_REFUND));
			} else {
				
				$collection->addFieldToFilter("{$main_table}.{$order_payment_state}",array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
						   ->addFieldToFilter("{$main_table}.{$payment_state}",array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN));
			}
						
			$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
			$collection->getSelect()->joinLeft($orderTable , 'main_table.order_id ='.$orderTable.'.increment_id',array('*'));
			
			$collection->getSelect()->group('id');
			$this->setCollection($collection);
		}
		//echo $collection->getSelect();die;
        return parent::_prepareCollection(true);
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
		
		if(Mage::helper('csorder')->isActive()){
			parent::_prepareColumns();
			$this->removeColumn('relation_id');
			$this->removeColumn('vendor_id');
			$this->removeColumn('order_payment_state');
			$this->removeColumn('payment_state');
			$this->removeColumn('action');
			$this->getColumn('increment_id')->setRenderer('');
			$params = $this->getRequest()->getParams();
			$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
			if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
				$this->removeColumn('qty_ready_to_pay');
				$this->removeColumn('qty_paid');
				$this->removeColumn('net_vendor_earn');
						   
			} else{
			
				$this->removeColumn('qty_ready_to_refund');
				$this->removeColumn('qty_refunded');
				$this->removeColumn('net_vendor_return');				
			}
			$this->addColumnAfter('relation_id', array(
				'header'=>Mage::helper('sales')->__('Select'),
				'sortable'=>false,
				'header_css_class' => 'a-center',
				'inline_css' => 'csmarketplace_relation_id checkbox',
				'index'=>'id',
				'type' => 'checkbox',
				'field_name' => 'in_orders',
				'values' => $this->_getSelectedOrders(),
				'disabled_values' => array(),
				'align' => 'center',
			), 'net_vendor_earn');
			
		}
		else
		{
			$this->addColumn('order_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Order ID#'),
  			'align'     => 'left',
   			'index'     => 'order_id',
  			'filter_index'  => 'order_id',
  			)); 
		
			$this->addColumn('base_order_total',array(
				'header'=> Mage::helper('catalog')->__('G.T. (Base)'),
				'index' => 'base_order_total',
				'type'          => 'currency',
				 'currency' => 'base_currency_code',
				
				));
			$this->addColumn('order_total',array(
				'header'=> Mage::helper('catalog')->__('G.T.'),
				'index' => 'order_total',
				'type'          => 'currency',
				 'currency' =>'currency',
				));
			
				
			$this->addColumn('shop_commission_fee',array(
				'header'=> Mage::helper('catalog')->__('Commission Fee'),
				'index' => 'shop_commission_fee',
				'type'          => 'currency',
				 'currency' =>'currency',
				
				));
				
			$this->addColumn('net_vendor_earn',array(
					'header'=> Mage::helper('catalog')->__('Vendor Payment'),
					'index' => 'net_vendor_earn',
					'type'          => 'currency',
					 'currency' =>'currency',
			));
			$this->addColumnAfter('relation_id', array(
				'header'=>Mage::helper('sales')->__('Select'),
				'sortable'=>false,
				'header_css_class' => 'a-center',
				'inline_css' => 'csmarketplace_relation_id checkbox',
				'index'=>'id',
				'type' => 'checkbox',
				'field_name' => 'in_orders',
				'values' => $this->_getSelectedOrders(),
				'disabled_values' => array(),
				'align' => 'center',
			), 'net_vendor_earn');
		}
        return $this;
    }

	/**
     * prepare return url
     *
     * @return string
     */
    public function getGridUrl()
    {
		return $this->getUrl('*/*/loadBlock', array('block'=>'search_grid', '_current' => true, 'collapse' => null));
    }
	
	/**
     * get selected orders
     *
     * @return array
     */
    protected function _getSelectedOrders()
    {
		$params = $this->getRequest()->getParams();
		$orderIds = isset($params['order_ids'])? explode(',',trim($params['order_ids'])):array();
        return $orderIds;
    }
    

    /**
     * Remove existing column
     *
     * @param string $columnId
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    public function removeColumn($columnId)
    {
    	if (isset($this->_columns[$columnId])) {
    		unset($this->_columns[$columnId]);
    		if ($this->_lastColumnId == $columnId) {
    			$this->_lastColumnId = key($this->_columns);
    		}
    	}
    	return $this;
    }
}