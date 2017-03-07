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
class Ced_CsOrder_Adminhtml_VorderController extends Mage_Adminhtml_Controller_Action {
	

   
	
    /**
     * Default vendor products list page
     */
    public function indexAction() {
        if(!$this->_getSession()->getVendorId()) return;
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );       
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ('Orders List') );
        
        $params = $this->getRequest()->getParams();
        if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
            Mage::getSingleton('core/session')->setData('order_filter',$params);
        }
    
        
        $this->renderLayout ();
    }


	/**
	 * Vendor CMS grid for AJAX request
	 */
	public function gridAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	
    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('csorder/vorders_list_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('csorder/vorders_list_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	 
	/**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null, $viewMode = false)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id',0);
        }
		$incrementId = 0;
        if ($orderId == 0) {
        	$incrementId = (int) $this->getRequest()->getParam('increment_id',0);
			 if (!$incrementId) {
				$this->_forward('noRoute');
				return false;
			}
        }

        if($orderId){
			$vorder = Mage::getModel('csmarketplace/vorders')->load($orderId);
        }
		else if($incrementId){
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$vorder = Mage::getModel('csmarketplace/vorders')->loadByField(array('order_id','vendor_id'),array($incrementId,$vendorId));
        }

        //add view mode for shipping method
		$order = $vorder->getOrder(false, $viewMode);


        if ($this->_canViewOrder($vorder)) {
	        Mage::register('current_order', $order);
			Mage::register('sales_order', $order);
			Mage::register('current_vorder', $vorder);
            return true;
        } else {
            $this->_redirect('csmarketplace/vorders');
        }
        return false;
    }
	
	 /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($vorder)
    {
		if(!$this->_getSession()->getVendorId()) return;
		$vendorId = Mage::getSingleton('customer/session')->getVendorId();
		 
		$incrementId = $vorder->getOrder()->getIncrementId();
		
		
		$collection = Mage::getModel('csmarketplace/vorders')->getCollection();
		$collection->addFieldToFilter('id', $vorder->getId())
					->addFieldToFilter('order_id', $incrementId)
					->addFieldToFilter('vendor_id', $vendorId);
		
		if(count($collection)>0){
			return true;
		}else{
			return false;
		}
    }
	
	/**
     * View order detale
     */
    public function viewAction()
    {
		
      
		
        $order = $this->_initOrder(true);
        $_extOrderId = '';

        $this->loadLayout(); 
        $this->_initLayoutMessages('customer/session');
         $this->_title($this->__('Order # %s %s | %s', $order->getRealOrderId(), $_extOrderId, Mage::helper('core')->formatDate($order->getCreatedAtDate(), 'medium', true)));
        $this->_initLayoutMessages('catalog/session');
        $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('csmarketplace/vorders/');
        }
        $this->renderLayout();
    }


    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder($viewMode = false)
    {
        $id = $this->getRequest()->getParam('order_id');
        $vorder = Mage::getModel("csmarketplace/vorders")->load($id);
        $id = $vorder->getOrder(false, $viewMode)->getId();
        
		//$order = Mage::getModel('sales/order')->load($id);
		
		$order = $vorder->getOrder(false, $viewMode);
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('current_vorder', $vorder);
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }


    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('csorder/adminhtml_sales_order_view_tab_invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('csorder/adminhtml_sales_order_view_tab_shipments')->toHtml()
        );
    }


    /**
     * Generate creditmemos grid for ajax request
     */
    public function creditmemosAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('csorder/adminhtml_sales_order_view_tab_creditmemos')->toHtml()
        );
    }

    

}
