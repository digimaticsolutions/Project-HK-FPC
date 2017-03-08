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

class Ced_CsOrder_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {

        $this->_objectId    = 'shipment_id';
        $this->_controller  = 'sales_order_shipment';
        $this->_mode        = 'view';

        parent::__construct();
        $this->setTemplate('csorder/widget/form/container.phtml');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_removeButton('save');
       
        
        if ($this->getShipment()->getId()) {
            $this->_addButton('print', array(
                'label'     => Mage::helper('sales')->__('Print Package Slip'),
                'class'     => 'btn btn-danger uptransform',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
			
			
        }
		
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
	 * Get Header Text
	 *
	 * @return string
	 */
    public function getHeaderText()
    {
        if ($this->getShipment()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('the shipment email was sent');
        }
        else {
            $emailSent = Mage::helper('sales')->__('the shipment email is not sent');
        }
        return Mage::helper('sales')->__('Shipment #%1$s | %3$s (%2$s)', $this->getShipment()->getIncrementId(), $emailSent, $this->formatDate($this->getShipment()->getCreatedAtDate(), 'medium', true));
    }

	/**
	 * get back url
	 *
	 * @return string
	 */
     public function getBackUrl()
    {
	
		$vorder = Mage::getModel("csmarketplace/vorders")->getVorderByShipment($this->getShipment());

        return $this->getUrl(
            'csorder/vorders/view',
            array(
                'order_id'  => $vorder->getId(),
                'active_tab'=> 'order_shipments',
				'_secure'=>true
            ));
    }

	/**
	 * get email url
	 *
	 * @return string
	 */
    public function getEmailUrl()
    {
        return $this->getUrl('*/sales_order_shipment/email', array('shipment_id'  => $this->getShipment()->getId(),'_secure'=>true));
    }


	/**
	 * Get print url
	 *
	 * @return string
	 */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'invoice_id' => $this->getShipment()->getId(),
			'_secure'=>true
        ));
    }
	
	/**
	 * get update back button url
	 *
	 * @return object
	 */
	 public function updateBackButtonUrl($flag)
    {
        if ($flag == "shipment") {
               return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('csorder/shipment') . '\')');
	        }
        return $this;
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
