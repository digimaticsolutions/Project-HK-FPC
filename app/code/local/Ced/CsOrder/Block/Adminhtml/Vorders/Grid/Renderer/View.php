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
class Ced_CsOrder_Block_Adminhtml_Vorders_Grid_Renderer_View extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	* Return the Order Id Link
	*
	*/
	public function render(Varien_Object $row){
		if($row->getVendorId()!=''){
			$order = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderId()); 
			$url =  $this->getUrl('adminhtml/adminhtml_vorder/view', array('order_id' => $row->getId(),'_secure'=>true));
			return "<a href='". $url . "'  >".$this->__('View')."</a>";
		}else 
    		 return ;
		} 
}