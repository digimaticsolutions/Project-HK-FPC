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
class Ced_CsTransaction_Block_Adminhtml_Vorders_Grid_Renderer_Paynow extends Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Paynow
{
	/**
     * get pay now button html
     *
     * @return string
     */
	protected function getPayNowButtonHtml($url = '')
    {
       return '<input class="button sacalable save" style=" background: #ffac47 url("images/btn_bg.gif") repeat-x scroll 0 100%;border-color: #ed6502 #a04300 #a04300 #ed6502;    border-style: solid;    border-width: 1px;    color: #fff;    cursor: pointer;    font: bold 12px arial,helvetica,sans-serif;    padding: 1px 7px 2px;text-align: center !important; white-space: nowrap;" type="button" onclick="setLocation(\''.$url.'\')" value="PayNow">';
    }
	
	/**
     * Get refund button html
     *
     * @return string
     */
	protected function getRefundButtonHtml($url = '',$label = '')
    {
       return '<input class="button sacalable save" style=" background: #ffac47 url("images/btn_bg.gif") repeat-x scroll 0 100%;border-color: #ed6502 #a04300 #a04300 #ed6502;    border-style: solid;    border-width: 1px;    color: #fff;    cursor: pointer;    font: bold 12px arial,helvetica,sans-serif;    padding: 1px 7px 2px;text-align: center !important; white-space: nowrap;" type="button" onclick="setLocation(\''.$url.'\')" value="RefundNow">';
    }
	
	/**
	* Return the Order Id Link
	*
	*/
	public function render(Varien_Object $row){
		if(!Mage::helper('csorder')->isActive()){
			return parent::render($row);
		}
		$vorderItem=Mage::getModel('cstransaction/vorder_items');
		$html='';
		if($row->getVendorId()!='') {
			$pending=true;
			if ($itemIds=$vorderItem->canPay($row->getVendorId(),$row->getOrderId())) {
				$pending=false;
				$url =  $this->getUrl('*/adminhtml_vpayments/new/', array('vendor_id' => $row->getVendorId(), 'order_ids'=>$itemIds,'type' => Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT));
				$html .="&nbsp;".$this->getPayNowButtonHtml($url);
			} 
			if ($itemIds=$vorderItem->canRefund($row->getVendorId(),$row->getOrderId())) {
				$pending=false;
				$url =  $this->getUrl('*/adminhtml_vpayments/new/', array('vendor_id' => $row->getVendorId(), 'order_ids'=>$itemIds,'type' => Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT));
				$html .= $this->getRefundButtonHtml($url,$html);
			}
			if($pending)
			{
				$html = parent::render($row);
			}
		}
		return $html;	
	}
}