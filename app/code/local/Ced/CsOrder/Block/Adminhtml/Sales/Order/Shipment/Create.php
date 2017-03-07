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
 * @author      CedCommerce Magento Core Team <magentocoreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_CsOrder_Block_Adminhtml_Sales_Order_Shipment_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Construct
	 */
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_shipment';
        $this->_mode = 'create';

        parent::__construct();
        $this->setTemplate('csorder/widget/form/container.phtml');
        //$this->_updateButton('save', 'label', Mage::helper('sales')->__('Submit Shipment'));
		$this->_removeButton('reset');
		$this->_removeButton('save');
        $this->_removeButton('delete');
		
		 $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back btn btn-warning',
        ), -1);
		
		
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

	/**
	 * get header text 
	 *
	 * @return string
	 */
    public function getHeaderText()
    {
        $header = Mage::helper('sales')->__('New Shipment for Order #%s', $this->getShipment()->getOrder()->getRealOrderId());
        return $header;
    }

	/**
	 * get back url
	 *
	 * @return string
	 */
    public function getBackUrl()
    {
        return $this->getUrl('csorder/vorders/view', array('order_id'=>$this->getRequest()->getParam('order_id'),'_secure'=>true));
    }
}
