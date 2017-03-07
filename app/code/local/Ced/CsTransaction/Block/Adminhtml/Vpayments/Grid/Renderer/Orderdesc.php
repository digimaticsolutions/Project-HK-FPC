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
 
class Ced_CsTransaction_Block_Adminhtml_Vpayments_Grid_Renderer_Orderdesc extends Ced_CsMarketplace_Block_Adminhtml_Vpayments_Grid_Renderer_Orderdesc
{
	
	/**
     * prepare html for transaction description
     *
     * @return string
     */
	public function render(Varien_Object $row)
	{
		if(Mage::helper('csorder')->isActive()){
			$amountDesc=$row->getItem_wise_amount_desc();
			$html='';
			$amountDesc=json_decode($amountDesc,true);
			if(is_array($amountDesc)){
				
				foreach ($amountDesc as $incrementId=>$amounts){
					if(is_array($amounts)){
						foreach($amounts as $item_id=>$baseNetAmount){
							if(is_array($baseNetAmount))
									return;
							$url = 'javascript:void(0);';
							$target = "";
							
							$amount = Mage::app()->getLocale()->currency($row->getBaseCurrency())->toCurrency($baseNetAmount);
							$vorder = Mage::getModel('sales/order')->load($incrementId);
							$incrementId=$vorder->getIncrementId();
							if ($this->_frontend && $vorder && $vorder->getId()) {
								$url =  Mage::getUrl("csmarketplace/vorders/view/",array('increment_id'=>$incrementId));
								$target = "target='_blank'";
								$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'."<a href='". $url . "' ".$target." >".$incrementId."</a>".'</label>, <b>Amount </b>'.$amount.'<br/>';
							}
							else 
							{
								$item=Mage::getModel('cstransaction/vorder_items')->load($item_id);
								$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'.$incrementId.' : '.$item->getSku().'</label>, <b>Amount </b>'.$amount.'<br/>';
							}
						}
					}
					
				}
				
			}
			else
			{
				$amountDesc=$row->getAmountDesc();
				if($amountDesc!=''){
					
					$amountDesc=json_decode($amountDesc,true);
					if(is_array($amountDesc)){
						foreach ($amountDesc as $incrementId=>$baseNetAmount){
								if(is_array($baseNetAmount))
									return;
								$url = 'javascript:void(0);';
								$target = "";
								$amount = Mage::app()->getLocale()->currency($row->getBaseCurrency())->toCurrency($baseNetAmount);
								$vorder = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
								if ($this->_frontend && $vorder && $vorder->getId()) {
									$url =  Mage::getUrl("csmarketplace/vorders/view/",array('increment_id'=>$incrementId));
									$target = "target='_blank'";
									$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'."<a href='". $url . "' ".$target." >".$incrementId."</a>".'</label>, <b>Amount </b>'.$amount.'<br/>';
								}
								else 
									$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'.$incrementId.'</label>, <b>Amount </b>'.$amount.'<br/>';
						}
					}
				}
			}
			return $html;	
		}
		return parent::render($row);
	}
 
}