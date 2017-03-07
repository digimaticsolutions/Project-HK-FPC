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
class Ced_CsOrder_InvoiceController extends Ced_CsMarketplace_Controller_AbstractController {
	
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
     * Get requested items qty's from request
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('invoice');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }
	/**
     * Initialize invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice($update = false)
    {
        $this->_title($this->__('Sales'))->_title($this->__('Invoices'));

        $invoice = false;
        $itemsToInvoice = 0;
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);

            if($invoice){
                $vorder = Mage::getModel("csmarketplace/vorders")->getVorderByInvoice($invoice);
                Mage::register('current_vorder', $vorder);
            }

            if (!$invoice->getId()) {
                $this->_getSession()->addError($this->__('The invoice no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $vorder      = Mage::getModel('csmarketplace/vorders')->load($orderId);
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$order = $vorder->getOrder();
            //$order->setShippingAmount(0);
			

            if(!Mage::helper('csorder')->canCreateInvoiceEnabled($vorder)){
                $this->_getSession()->addError($this->__('Not allowed to create Invoice.'));
                return false;
            }

			/* Order belong to current vendor
			*/
			if($vorder->getVendorId()!==$vendorId){
                $this->_getSession()->addError($this->__('The order no exist for Current Vendor.'));
                return false;
            }

			
            /**
             * Check order existing
             */
            if (!$vorder->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
			
			
            /**
             * Check invoice create availability
             */
            if (!$vorder->canInvoice()) {
                $this->_getSession()->addError($this->__('The order does not allow creating an invoice.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys();
            
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
            
            if (!$invoice->getTotalQty()) {
                Mage::throwException($this->__('Cannot create an invoice without products.'));
            }
         Mage::register('current_vorder', $vorder);
        }
        $order = $invoice->getOrder();
        
        Mage::register('current_invoice', $invoice);
        return $invoice;
    }
	 /**
     * Invoice create page
     */
    public function newAction()
    {
        $invoice = $this->_initInvoice();



        if ($invoice) {
            $this->_title($this->__('New Invoice'));
            Mage::getModel('csorder/invoice')->updateTotal($invoice);      
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
			
			$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
			if ($navigationBlock) {
				$navigationBlock->setActive('csmarketplace/vorders/');
			}
            $this->renderLayout();
        } else {
            $this->_redirect('csorder/vorders/view', array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
    }
	
	
	/**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        $vorderId = $this->getRequest()->getParam('order_id');
		$vorder = Mage::getModel("csmarketplace/vorders")->load($vorderId);
		$orderId = $vorder->getOrder()->getId();
		

        

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {
            


                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send the shipment email.'));
                    }
                }
                $this->_redirect('csorder/vorders/view', array('order_id' => $vorderId));
            } else {
                $this->_redirect('*/*/new', array('order_id' => $vorderId));
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $vorderId));
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
				$navigationBlock->setActive('csmarketplace/invoice/');
			}
			
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ('Invoice List') );
		
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


    public function pdfinvoicesAction(){
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        $invoicesIds = explode(",", $invoicesIds);
        if (!empty($invoicesIds)) {
            $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('csorder/sales_order_pdf_invoice')->getPdf($invoices);
            } else {
                $pages = Mage::getModel('csorder/sales_order_pdf_invoice')->getPdf($invoices);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }


     /**
     * Export invoice grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'invoices.csv';
        $grid       = $this->getLayout()->createBlock('csorder/invoice_list_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
    *  Export shipment grid to Excel XML format
    */
    public function exportExcelAction()
    {
        $fileName   = 'shipments.xml';
        $grid       = $this->getLayout()->createBlock('csorder/invoice_list_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    


    /**
     * Invoice information page
     */
    public function viewAction()
    {
        $invoice = $this->_initInvoice();

        Mage::getModel('csorder/invoice')->updateTotal($invoice);


        if ($invoice) {
            $this->_title(sprintf("#%s", $invoice->getIncrementId()));

            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');

            $this->getLayout()->getBlock('sales_invoice_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));


            
            $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('csmarketplace/invoice/');
            }

            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }


	/**
     * Action for adding comment
     *
     * @return html
     */
    public function addCommentAction()
    {
        try {
            $this->getRequest()->setParam('invoice_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('The Comment Text field cannot be empty.'));
            }
            $invoice = $this->_initInvoice();
            $invoice->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $invoice->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);//developer@cedcommerce
            $invoice->save();
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$block=$this->getLayout()->createBlock('adminhtml/sales_order_invoice_view_comments','invoice_comments');
            $shipmentBlock =$this->getLayout()->createBlock('adminhtml/sales_order_comments_view','order_comments')->setTemplate('sales/order/comments/view.phtml');
			$block->append($shipmentBlock);
            $response = $block->toHtml(); 
            /* $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $response = $this->getLayout()->getBlock('invoice_comments')->toHtml(); */
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

	/**
     * Action for printing pdf
     *
     */
    public function printAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                if($invoice){
                    $vorder = Mage::getModel("csmarketplace/vorders")->getVorderByInvoice($invoice);
                    Mage::register('current_vorder', $vorder);
                }
                Mage::getModel('csorder/invoice')->updateTotal($invoice);
                $pdf = Mage::getModel('csorder/sales_order_pdf_invoice')->getPdf(array($invoice));
                $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Update items qty action
     */
    public function updateQtyAction()
    {


        try {
            $invoice = $this->_initInvoice(true);
            // Save invoice comment text in current invoice object in order to display it in corresponding view
            $invoiceRawData = $this->getRequest()->getParam('invoice');
            $invoiceRawCommentText = $invoiceRawData['comment_text'];
            
            Mage::getModel('csorder/invoice')->updateTotal($invoice);      

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
                'message'   => $this->__('Cannot update item quantity.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } 
        $this->getResponse()->setBody($response);
    }


    /**
     * Prepare shipment
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _prepareShipment($invoice)
    {
        $savedQtys = $this->_getItemQtys();
        $shipment = Mage::getModel('sales/service_order', $invoice->getOrder())->prepareShipment($savedQtys);
        if (!$shipment->getTotalQty()) {
            return false;
        }


        $shipment->register();
        $tracks = $this->getRequest()->getPost('tracking');
        if ($tracks) {
            foreach ($tracks as $data) {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->addData($data);
                $shipment->addTrack($track);
            }
        }
        return $shipment;
    }

}