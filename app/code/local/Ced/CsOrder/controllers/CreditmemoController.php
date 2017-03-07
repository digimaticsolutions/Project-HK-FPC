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
class Ced_CsOrder_CreditmemoController extends Ced_CsMarketplace_Controller_AbstractController {
	
     /**
     * Action predispatch
     * Check that vendpr is eligible for viewing content
     */
    public function preDispatch()
    {
       parent::preDispatch();
       if (!$this->getRequest()->isDispatched()) {
            return;
       }
       if(!Mage::helper('csorder')->isActive()){
          $this->_redirect('csmarketplace/vorders');
       }
    }
    
    /**
	 * Default vendor Shipment list page
	 */
	public function indexAction() {

		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout ();
        $this->_initLayoutMessages('customer/session');
		 $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
	        if ($navigationBlock) {
	            $navigationBlock->setActive('csorder/creditmemo/');
	        }
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ('Credit memo List') );
		
		$params = $this->getRequest()->getParams();
		if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
			Mage::getSingleton('core/session')->setData('order_filter',$params);
		}
	
		
		$this->renderLayout ();
	}
	
    /**
     * Check if creditmeno can be created for order
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _canCreditmemo($order)
    { 
        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('The order no longer exists.'));
            return false;
        }

        /**
         * Check creditmemo create availability
         */
        if (!$order->canCreditmemo()) {
            $this->_getSession()->addError($this->__('Cannot create credit memo for the order.'));
            return false;
        }
        return true;
    }
     /**
     * Get requested items qtys and return to stock flags
     */
    protected function _getItemData()
    {
        $data = $this->getRequest()->getParam('creditmemo');
        if (!$data) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        }

        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

     /**
     * Initialize requested invoice instance
     * @param unknown_type $order
     */
    protected function _initInvoice($order)
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')
                ->load($invoiceId)
                ->setOrder($order);
            if ($invoice->getId()) {
                return $invoice;
            }
        }
        return false;
    }

	/**
     * Initialize creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _initCreditmemo($viewMode = false)
    { 
        $this->_title($this->__('Sales'))->_title($this->__('Credit Memos'));

        $creditmemo = false;
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($creditmemoId) {
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
            
             if($vorder = Mage::getModel('csmarketplace/vorders')->getVorderByCreditmemo($creditmemo)){
                Mage::register("current_vorder", $vorder);    
                //$creditmemo->setOrder($vorder->getOrder(false, $viewMode));
            }
        } elseif ($orderId) {
            $data   = $this->getRequest()->getParam('creditmemo');
            $vorder      = Mage::getModel('csmarketplace/vorders')->load($orderId);
            Mage::register("current_vorder", $vorder);
            $vendorId = Mage::getSingleton('customer/session')->getVendorId();
            
            //$order = $vorder->getOrder(false, $viewMode);
            $order = $vorder->getOrder();
           
            $invoice = $this->_initInvoice($order);



            if (!$this->_canCreditmemo($order)) {
                return false;
            }

            if(!Mage::helper('csorder')->canCreateCreditmemoEnabled($vorder)){
                $this->_getSession()->addError($this->__('Not allowed to create Credit Memo.'));
                return false;
            }

            /* Order belong to current vendor
            */
            if($vorder->getVendorId()!==$vendorId){
                $this->_getSession()->addError($this->__('The order no exist for Current Vendor.'));
                return false;
            }

            $savedData = $this->_getItemData();

            $qtys = array();
            $backToStock = array();
            foreach ($savedData as $orderItemId =>$itemData) {
                if (isset($itemData['qty'])) {
                    $qtys[$orderItemId] = $itemData['qty'];
                }
                if (isset($itemData['back_to_stock'])) {
                    $backToStock[$orderItemId] = true;
                }
            }
            $data['qtys'] = $qtys;

            $service = Mage::getModel('sales/service_order', $order);
            if ($invoice) {
                $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
            } else {
                $creditmemo = $service->prepareCreditmemo($data);
            }

            /**
             * Process back to stock flags
             */
            foreach ($creditmemo->getAllItems() as $creditmemoItem) {
                $orderItem = $creditmemoItem->getOrderItem();
                $parentId = $orderItem->getParentItemId();
                if (isset($backToStock[$orderItem->getId()])) {
                    $creditmemoItem->setBackToStock(true);
                } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                    $creditmemoItem->setBackToStock(true);
                } elseif (empty($savedData)) {
                    $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
                } else {
                    $creditmemoItem->setBackToStock(false);
                }
            }
        }

        $args = array('creditmemo' => $creditmemo, 'request' => $this->getRequest());
        Mage::dispatchEvent('adminhtml_sales_order_creditmemo_register_before', $args);

        Mage::register('current_creditmemo', $creditmemo);
        return $creditmemo;
    }
	
	/**
     * Shipment information page
     */
    public function viewAction()
    { 
        $creditmemo = $this->_initCreditmemo(true);
        Mage::getModel('csorder/creditmemo')->updateTotal($creditmemo);
        
        if ($creditmemo) {
            if ($creditmemo->getInvoice()) {
                $this->_title($this->__("View Memo for #%s", $creditmemo->getInvoice()->getIncrementId()));
            } else {
                $this->_title($this->__("View Memo"));
            }

            $this->loadLayout();

            $this->getLayout()->getBlock('sales_creditmemo_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));

            $this->_initLayoutMessages('customer/session');
            $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
	        if ($navigationBlock) {
	            $navigationBlock->setActive('csorder/creditmemo/');
	        }
            $this->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }


    /**
     * creditmemo create page
     */
    public function newAction()
    {
        if ($creditmemo = $this->_initCreditmemo()) {
            Mage::getModel('csorder/creditmemo')->updateTotal($creditmemo);
            if ($creditmemo->getInvoice()) {
                $this->_title($this->__("New Memo for #%s", $creditmemo->getInvoice()->getIncrementId()));
            } else {
                $this->_title($this->__("New Memo"));
            }
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('csorder/creditmemo/');
            }
            $this->renderLayout();
        } else {
              $this->_redirect('csorder/vorders/view' , array("order_id"=> $this->getRequest()->getParam("order_id")));
        }
    }

	/**
     * Create pdf for current creditmemo
     */
    public function printAction()
    {
        $this->_initCreditmemo();
         /** @see Mage_Adminhtml_Sales_Order_InvoiceController */
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId)) {
                Mage::getModel('csorder/creditmemo')->updateTotal($creditmemo);


                $pdf = Mage::getModel('csorder/sales_order_pdf_creditmemo')->getPdf(array($creditmemo));
                $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }
	/**
     * Action for printing pdf of credit memo
     */
	 public function pdfcreditmemosAction(){
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        $creditmemosIds = explode(",", $creditmemosIds);
        if (!empty($creditmemosIds)) {
            $invoices = Mage::getResourceModel('sales/order_creditmemo_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('csorder/sales_order_pdf_creditmemo')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('csorder/sales_order_pdf_creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }
	
	/**
     * Export credit memo grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'creditmemos.csv';
        $grid       = $this->getLayout()->createBlock('csorder/creditmemo_list_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export credit memo grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'creditmemos.xml';
        $grid       = $this->getLayout()->createBlock('csorder/creditmemo_list_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }


    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {
        try {
            $creditmemo = $this->_initCreditmemo(true);
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $response = $this->getLayout()->getBlock('order_items')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot update the item\'s quantity.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

     /**
     * Save creditmemo
     * We can save only new creditmemo. Existing creditmemos are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('creditmemo');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $creditmemo = $this->_initCreditmemo();
            if ($creditmemo) {
                if (($creditmemo->getGrandTotal() <=0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                    Mage::throwException(
                        $this->__('Credit memo\'s total must be positive.')
                    );
                }

                $comment = '';
                if (!empty($data['comment_text'])) {
                    $creditmemo->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                    if (isset($data['comment_customer_notify'])) {
                        $comment = $data['comment_text'];
                    }
                }

                if (isset($data['do_refund'])) {
                    $creditmemo->setRefundRequested(true);
                }
                if (isset($data['do_offline'])) {
                    $creditmemo->setOfflineRequested((bool)(int)$data['do_offline']);
                }

                $creditmemo->register();
                if (!empty($data['send_email'])) {
                    $creditmemo->setEmailSent(true);
                }

                $creditmemo->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $this->_saveCreditmemo($creditmemo);
                $creditmemo->sendEmail(!empty($data['send_email']), $comment);
                $this->_getSession()->addSuccess($this->__('The credit memo has been created.'));
                $this->_redirect('*/vorders/view', array('order_id' => $this->getRequest()->getParam('order_id')));
                return;
            } else {
                $this->_forward('noRoute');
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot save the credit memo.'));
        }
        $this->_redirect('*/*/new', array('_current' => true));
    }

     /**
     * Save creditmemo and related order, invoice in one transaction
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    protected function _saveCreditmemo($creditmemo)
    {
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder());
        if ($creditmemo->getInvoice()) {
            $transactionSave->addObject($creditmemo->getInvoice());
        }
        $transactionSave->save();

        return $this;
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
     * Add comment to creditmemo history
     */
    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam(
                'creditmemo_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('The Comment Text field cannot be empty.'));
            }
            $creditmemo = $this->_initCreditmemo();
            $creditmemo->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $creditmemo->save();
            $creditmemo->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$block=$this->getLayout()->createBlock('adminhtml/sales_order_creditmemo_view_comments','creditmemo_comments');
            $shipmentBlock =$this->getLayout()->createBlock('adminhtml/sales_order_comments_view','order_comments')->setTemplate('sales/order/comments/view.phtml');
			$block->append($shipmentBlock);
            $response = $block->toHtml(); 
           /*  $this->loadLayout();
            $response = $this->getLayout()->getBlock('creditmemo_comments')->toHtml(); */
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }
}