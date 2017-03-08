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
 * @package     Ced_CsProductfaq
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsProductfaq_Model_Observer {
  protected static $_singletonFlag = false;
  /**
   * Save product Data after save
   */
  public function saveProductTabData(Varien_Event_Observer $observer) {
  
  	if(Mage::getDesign()->getArea() == 'frontend')
  	{
	    $product = $observer->getEvent()->getProduct();
	    //print_r($product->getData());
	   
	    
	    $productid = $product->getEntityId();
	    $vid =  $product->getVendorId();
	
	    $data = $this->_getRequest()->getParams();
	    $title = $this->_getRequest()->getPost('title');
	    $description = $this->_getRequest()->getPost('description');
	    $vid =  Mage::getSingleton('customer/session')->getVendorId();
	   // print_r($vid);die("l");
	    for($i = 1; $i <= count($title); $i ++) {
	      $titles = $title ['quest'.$i];
	      $descriptions = $description ['desc'.$i];
	      $visible = '1';
	      try {
	        if ($titles) {
	          $model = $this->getModel ();
	          $model->setData ('product_id',$productid )
			        ->setData ('title',$titles)
			        ->setData ('visible_on_frontend', $visible)
			        ->setData ('vendor_id', $vid)
			        ->setData ('description', $descriptions);
	          $model->save();
	        } else {
	          Mage::throwException ( 'Some Message' );
	        }
	      } catch ( Exception $e ) {
	        Mage::getSingleton ( 'adminhtml/session' )->addError($e->getMessage());
	      }
	    }
  	}
  }
  /**
   * Get Current Rpoduct
   */
  public function getProduct() {
    return Mage::registry ( 'product' );
  }
  /**
   * Get FAQ Model
   */
  public function getModel() {
    return Mage::getModel ( 'productfaq/productfaq' );
  }
  /**
   * GetRequest
   */
  protected function _getRequest() {
    return Mage::app()->getRequest();
  }
  /**
   * Predispath admin action controller
   *
   * @param Varien_Event_Observer $observer
   */

  


}
