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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_Shipment_Renderer_Totalqty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract

{
	/**
	 * Render update total qty
	 *
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		$shipment = Mage::getModel('sales/order_shipment')->load($row->getId());
		$shipment = Mage::getModel('csorder/shipment')->updateTotalqty($shipment);
		return $shipment;
	}
 
}