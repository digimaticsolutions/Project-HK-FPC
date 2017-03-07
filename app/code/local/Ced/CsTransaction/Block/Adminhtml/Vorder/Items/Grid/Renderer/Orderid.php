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
 * @package     Ced_CsTransaction 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsTransaction_Block_Adminhtml_Vorder_Items_Grid_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
	
		/**
		 * Prepare orderId link
		 *
		 * @return string
		 */
		public function render(Varien_Object $row){
			if($row->getOrderIncrementId()!=''){	  
				 $orderId =$row->getOrderId();
				 $url =  Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view", array('order_id' => $orderId));			
				$html='<a href="#popup" onClick="javascript:openMyPopup(\''.$url.'\')" >'.$row->getOrderIncrementId().'</a></br>Item : '.$row->getSku();
				return $html;  
			  }            
			else 
				return '';
       }
}