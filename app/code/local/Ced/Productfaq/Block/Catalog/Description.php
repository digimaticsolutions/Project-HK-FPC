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
  * @package     Ced_Productfaq
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Productfaq_Block_Catalog_Description extends  Mage_Catalog_Block_Product_View_Description
{
	/*Getting Products*/
	function getProduct()
	{
		if (!$this->_product)
		 {
			$this->_product = Mage::registry('product');
		 }
		  return $this->_product;
	}
	/*Getting Questions collection related to product */
	public function getQuestions()
	    {
			 $_product = $this->getProduct();
			$productid=$_product->getId();
			$questions=Mage::getModel('productfaq/productfaq')->getCollection()->addFieldToFilter('product_id',$productid);
			return $questions;
		}
		/*Getting questions likes count related to product*/
    public function getLikesCount($questionid)
	    { 
				$_product = $this->getProduct();
				$productid=$_product->getId();
				$likes=Mage::getModel('productfaq/like')->getCollection()->addFieldToFilter('question_id',$questionid)->addFieldToFilter('product_id',$productid);
				return $likes;
		}
	
}
?>
