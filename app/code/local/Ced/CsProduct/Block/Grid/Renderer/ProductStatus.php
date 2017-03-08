<?php 

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsProduct
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
 
class Ced_CsProduct_Block_Grid_Renderer_ProductStatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
		$vOptionArray=Mage::getSingleton('csmarketplace/vproducts')->getVendorOptionArray();
		if($row->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
			return $vOptionArray[$row->getCheckStatus().$row->getStatus()];
		else 
			return $vOptionArray[$row->getCheckStatus()];
	}

}
?>