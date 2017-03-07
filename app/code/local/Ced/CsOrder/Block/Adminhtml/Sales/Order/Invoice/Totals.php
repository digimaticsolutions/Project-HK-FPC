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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
{
    protected $_invoice = null;

	/**
     * Get Invoice
     *
     * @return object
     */
    public function getInvoice()
    {
		$vorder = $this->getVorders();
        if ($this->_invoice === null) {
            if ($this->hasData('invoice')) {
                $this->_invoice = $this->_getData('invoice');
            } elseif (Mage::registry('current_invoice')) {
                $this->_invoice = Mage::registry('current_invoice');
            } elseif ($this->getParentBlock()->getInvoice()) {
                $this->_invoice = $this->getParentBlock()->getInvoice();
            }
        }


		//set Shipping amount
		if(!$vorder->isAdvanceOrder()){
			$this->_invoice->setShippingAmount(0);
			$this->_invoice->setGrandTotal($this->_invoice->getSubtotal());
		}

        return $this->_invoice;
    }
    
    public function getSource()
    {
        return $this->getInvoice();
    }
    
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
		$vorder = $this->getVorders();
		parent::_initTotals();
		
    }
	
	
	
	/**
     * Get Vorders
     *
     * @return object
     */
	public function getVorders(){
		return Mage::registry('current_vorder');
	}
}
