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

class Ced_CsOrder_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

	/**
	 * Constructor
	 *
	 */
    public function __construct()
    {
		
        $this->_objectId    = 'order_id';
        $this->_controller  = 'sales_order';
        $this->_mode        = 'view';
		$this->_blockGroup = "adminhtml";

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('sales_order_view');
        $order = $this->getOrder();
        $vorder = $this->getVorder();

		
        if (Mage::helper('csorder')->canCreateCreditmemoEnabled($vorder) && $this->_isAllowedAction('creditmemo') && $vorder->canCreditmemo()) {
            $message = Mage::helper('sales')->__('This will create an offline refund. To create an online refund, open an invoice and create credit memo for it. Do you wish to proceed?');
            $onClick = "window.open('{$this->getCreditmemoUrl()}')";
            if ($order->getPayment()->getMethodInstance()->isGateway()) {
                $onClick = "confirmSetLocation('{$message}', '{$this->getCreditmemoUrl()}')";
            }
            $this->_addButton('order_creditmemo', array(
                'label'     => Mage::helper('sales')->__('Credit Memo'),
                'onclick'   => $onClick,
                'class'     => 'btn btn-success uptransform'
            ));
        }


		
        if (Mage::helper('csorder')->canCreateInvoiceEnabled($vorder) && $this->_isAllowedAction('invoice') && $vorder->canInvoice()) {
            $_label = $order->getForcedDoShipmentWithInvoice() ?
                Mage::helper('sales')->__('Invoice and Ship') :
                Mage::helper('sales')->__('Invoice');
            $this->_addButton('order_invoice', array(
                'label'     => $_label,
                'onclick'   => 'window.open(\'' . $this->getInvoiceUrl() . '\')',
                'class'     => 'btn btn-info uptransform'
            ));
        }
		


        if (Mage::helper('csorder')->canCreateShipmentEnabled($vorder) && $this->_isAllowedAction('ship') && $vorder->canShip()
            && !$order->getForcedDoShipmentWithInvoice()) {
            $this->_addButton('order_ship', array(
                'label'     => Mage::helper('sales')->__('Ship'),
                'onclick'   => 'window.open(\'' . $this->getShipUrl() . '\')',
                'class'     => 'btn btn-success uptransform'
            ));
        }
		if(!Mage::app()->getStore()->isAdmin())
		$this->_addButton('print_order', array(
			'label'     => Mage::helper('sales')->__('Print Order'),
			'onclick'   => 'window.open(\'' . $this->getPrintUrlVorder() . '\')',
			'class'     => 'btn btn-danger uptransform'
		));
		
		 $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back btn btn-warning',
        ), -1);

        
    }



    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }
	
	
	/**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getVorder()
    {
        return Mage::registry('current_vorder');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }


	/**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getVorderId()
    {
        return $this->getVorder()->getId();
    }

	/**
	 * get header text
	 *
	 * @return string
	 */
    public function getHeaderText()
    {
        if ($_extOrderId = $this->getOrder()->getExtOrderId()) {
            $_extOrderId = '[' . $_extOrderId . '] ';
        } else {
            $_extOrderId = '';
        }
        return Mage::helper('sales')->__('Order # %s %s | %s', $this->getOrder()->getRealOrderId(), $_extOrderId, $this->formatDate($this->getOrder()->getCreatedAtDate(), 'medium', true));
    }

	/**
	 * get url
	 *
	 * @return string
	 */
    public function getUrl($params='', $params2=array())
    {
        $params2['order_id'] = $this->getVorderId();
        return parent::getUrl($params, $params2);
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/sales_order_edit/start');
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email',array('_secure'=>true));
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel',array('_secure'=>true));
    }

    public function getInvoiceUrl()
    {
        return $this->getUrl('csorder/invoice/new',array('_secure'=>true));
    }

    public function getCreditmemoUrl()
    {
        return $this->getUrl('*/creditmemo/new',array('_secure'=>true));
    }

    public function getHoldUrl()
    {
        return $this->getUrl('*/*/hold',array('_secure'=>true));
    }

    public function getUnholdUrl()
    {
        return $this->getUrl('*/*/unhold',array('_secure'=>true));
    }

    public function getShipUrl()
    {
        return $this->getUrl('csorder/shipment/new',array('_secure'=>true));
    }

    public function getCommentUrl()
    {
        return $this->getUrl('*/*/comment',array('_secure'=>true));
    }

    public function getReorderUrl()
    {
        return $this->getUrl('*/sales_order_create/reorder',array('_secure'=>true));
    }

    /**
     * Payment void URL getter
     */
    public function getVoidPaymentUrl()
    {
        return $this->getUrl('*/*/voidPayment',array('_secure'=>true));
    }

    protected function _isAllowedAction($action)
    {
		return true;
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
		if(Mage::app()->getStore()->isAdmin())
		{
			return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_vendororder');
		}
		else
		{
			return $this->getUrl('csmarketplace/vorders/',array('_secure'=>true));
		}
    }

    public function getReviewPaymentUrl($action)
    {
        return $this->getUrl('*/*/reviewPayment', array('action' => $action,'_secure'=>true));
    }
	
	
	public function getPrintUrlVorder()
    {

        return $this->getUrl('csmarketplace/vorders/print',array('_secure'=>true));
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
