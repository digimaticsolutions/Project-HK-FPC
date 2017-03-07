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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
{
	/**
     * Get Update Url
     *
     * @return string
     */
	public function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', array(
                'order_id'=>$this->getRequest()->getParam('order_id', null),
                'invoice_id'=>$this->getRequest()->getParam('invoice_id', null),'_secure'=>true
        ));
    }
	
	/**
     * Get Current Vorder
     *
     * @return string
     */
	public function getVorder(){
		return Mage::registry('current_vorder');
	}
}
