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
class Ced_Productfaq_Model_Observer {
  protected static $_singletonFlag = false;
  /**
   * Save product Data after save
   */
  public function saveProductTabData(Varien_Event_Observer $observer) {
  	
  	if(Mage::getDesign()->getArea() == 'frontend'){
  		return ;
  	}
  	
    if (! self::$_singletonFlag) {
      self::$_singletonFlag = true;
      $product = $observer->getEvent()->getProduct();
    }
    $productid = $product->getId ();
    $data = $this->_getRequest ()->getParams ();
    $title = $this->_getRequest ()->getPost ( 'title' );
    
    $description = $this->_getRequest ()->getPost ( 'description' );
    
    for($i = 1; $i <= count ( $title ); $i ++) {
      $titles = $title ['quest' . $i];
      $descriptions = $description ['desc' . $i];
      $visible = '1';
      try {
        if ($titles) {
          $model = $this->getModel ();
          $model->setData ( 'product_id', $productid )->setData ( 'title', $titles )->setData ( 'visible_on_frontend', $visible )->setData ( 'description', $descriptions );
          $model->save ();
        } else {
          Mage::throwException ( 'Some Message' );
        }
      } catch ( Exception $e ) {
        Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
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
    return Mage::app ()->getRequest ();
  }
  /**
   * Predispath admin action controller
   *
   * @param Varien_Event_Observer $observer
   */
  public function preDispatch(Varien_Event_Observer $observer)
  {
    if (Mage::getSingleton('admin/session')->isLoggedIn()) {
      $feedModel  = Mage::getModel('productfaq/feed');
      /* @var $feedModel Ced_Core_Model_Feed */
      $feedModel->checkUpdate();
  
    }
  }
  
  public function beforeLoadingLayout(Varien_Event_Observer $observer) {
    try {
      
      $action = $observer->getEvent()->getAction();
      $layout = $observer->getEvent()->getLayout();
      $sec=$action->getRequest()->getParam('section');
      
      /* print_r($layout->getUpdate()->getHandles());die('observer'); */
      if($action->getRequest()->getActionName() == 'cedpop') return $this;
      $modules = Mage::helper('productfaq')->getCedCommerceExtensions();
       if(preg_match('/ced/i',strtolower($action->getRequest()->getControllerModule()))){

        foreach ($modules as $moduleName=>$releaseVersion)
        {   

          $m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }  $h = Mage::getStoreConfig(Ced_Productfaq_Block_Extensions::HASH_PATH_PREFIX.$m.'_hash'); for($i=1;$i<=(int)Mage::getStoreConfig(Ced_Productfaq_Block_Extensions::HASH_PATH_PREFIX.$m.'_level');$i++){$h = base64_decode($h);}$h = json_decode($h,true); 
          if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && $h['module_name'] == $m && $h['license'] == Mage::getStoreConfig(Ced_Productfaq_Block_Extensions::HASH_PATH_PREFIX.$m)){}else{ $_POST=$_GET=array();$action->getRequest()->setParams(array());$exist = false; foreach($layout->getUpdate()->getHandles() as $handle){ if($handle=='c_e_d_c_o_m_m_e_r_c_e'){ $exist = true; break; } } if(!$exist){ $layout->getUpdate()->addHandle('c_e_d_c_o_m_m_e_r_c_e'); }}  
        }
      }
      return $this;
      
    } catch (Exception $e) {
      return $this;
    }
  }

}
