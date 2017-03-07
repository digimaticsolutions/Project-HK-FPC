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
 
class Ced_CsProduct_Block_Grid_Renderer_ProductImage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
		$storeId=$this->getRequest()->getParam('store',0);
		$_product=Mage::getModel('catalog/product')->setStoreId($storeId)->load($row->getEntityId());
		if($_product && $_product->getId()){
			if($storeId==0 && Mage::app()->isSingleStoreMode())
				$productUrl=$_product->getProductUrl();
			else if($storeId!=0)
				$productUrl=$_product->getUrlInStore(array('_store'=>$storeId));
			$html='';
			if(($row->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS) && (($row->getStatus()==Mage_Catalog_Model_Product_Status::STATUS_ENABLED && $storeId!=0)||($storeId==0 && Mage::app()->isSingleStoreMode()))){
				$html='<div style="text-align:center"><a href="'.$productUrl.'" target="_blank">';
				$html.='<img title="'.$row->getName().'" id='.$row->getId()." width='70' height='35' src='".Mage::helper('catalog/image')->init($_product, 'thumbnail',$_product->getImage())->constrainOnly(true)->resize(70, 35)."'/></a></div>";
				$html.='<div><a style="color:#337ab7;" target="_blank" href="'.$productUrl.'">'.$row->getName().'</a></div>';
			}else {
				$html='<div style="text-align:center"><img id='.$row->getId()." width='70' height='35' src='".Mage::helper('catalog/image')->init($_product, 'thumbnail',$_product->getImage())->constrainOnly(true)->resize(70, 35)."'/></div><div>";
				$html.=$row->getName().'</div>';
			}
		}
		return $html;
	}

}
?>