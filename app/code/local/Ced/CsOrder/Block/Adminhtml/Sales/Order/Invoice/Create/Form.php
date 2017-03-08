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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_Invoice_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getInvoice()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }
	/**
     * Prepare layout
     *
     * @return object
     */
    protected function _prepareLayout()
    {

        $trackingBlock = $this->getLayout()->createBlock('adminhtml/sales_order_invoice_create_tracking');
        $this->setChild('tracking', $trackingBlock);
        return parent::_prepareLayout();
    }

	/**
     * Get Save Url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getRequest()->getParam('order_id'),'_secure'=>true));
    }

	/**
     * Checks shipment can be created or not
     *
     * @return boolean
     */
    public function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->getQtyToShip()) {
                return true;
            }
        }
        return false;
    }

	/**
     * Has invoice shipment type mismatch
     *
     * @return boolean
     */
    public function hasInvoiceShipmentTypeMismatch() {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->isChildrenCalculated() && !$item->getOrderItem()->isShipSeparately()) {
                return true;
            }
        }
        return false;
    }

	/**
     * checks order can be shipped partially or not
     *
     * @return boolean
     */
    public function canShipPartiallyItem()
    {
        $value = $this->getOrder()->getCanShipPartiallyItem();
        if (!is_null($value) && !$value) {
            return false;
        }
        return true;
    }

    /**
     * Return forced creating of shipment flag
     *
     * @return integer
     */
    public function getForcedShipmentCreate()
    {
        return (int) $this->getOrder()->getForcedDoShipmentWithInvoice();
    }
}
