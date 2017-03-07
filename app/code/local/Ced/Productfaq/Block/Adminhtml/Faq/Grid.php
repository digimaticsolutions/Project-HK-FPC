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
class Ced_Productfaq_Block_Adminhtml_Faq_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  /**
   * Modify id,default sort & direction
   */
  public function __construct() {
    parent::__construct ();
    $this->setId ( 'product_faq_grid' );
    $this->setDefaultSort ( 'id' );
    $this->setDefaultDir ( 'DESC' );
    $this->setSaveParametersInSession ( false );
  }
  /**
   * Preparing faq collection
   */
  protected function _prepareCollection() {
    $collection = Mage::getModel ( 'productfaq/productfaq' )->getCollection ();
    // var_dump($collection->getData());die;
    $this->setCollection ( $collection );
    return parent::_prepareCollection ();
  }
  /**
   * Preparing columns for grid
   */
  protected function _prepareColumns() {
    $this->addColumn ( 'id', array (
        'header' => 'id',
        'width' => '80px',
        'index' => 'id',
        'type' => 'number' 
    ) );
    $this->addColumn ( 'product_id', array (
        'header' => Mage::helper ( 'productfaq' )->__ ( 'Product Id' ),
        'width' => '80px',
        'index' => 'product_id' 
    ) );
    $this->addColumn ( 'title', array (
        'header' => Mage::helper ( 'productfaq' )->__ ( 'Title/Question' ),
        'width' => '80px',
        'index' => 'title' 
    ) );
    
    $this->addColumn ( 'email_id', array (
        'header' => Mage::helper ( 'productfaq' )->__ ( 'Email ID' ),
        'width' => '80px',
        'index' => 'email_id' 
    ) );
    $this->addColumn ( 'visible_on_frontend', array (
        'header' => Mage::helper ( 'productfaq' )->__ ( 'Visible On Frontend' ),
        'width' => '140px',
        'index' => 'visible_on_frontend' 
    ) );
    $this->addColumn ( 'action', array (
        'header' => Mage::helper ( 'productfaq' )->__ ( 'Action' ),
        'width' => '80px',
        'type' => 'action',
        'getter' => 'getId',
        'actions' => array (
            array (
                'caption' => Mage::helper ( 'productfaq' )->__ ( 'edit' ),
                'url' => array (
                    'base' => 'adminhtml/adminhtml_faq/edit' 
                ),
                'field' => 'id',
                'confirm' => Mage::helper ( 'productfaq' )->__ ( "Are you sure?\nThis operation will just delete the record from your local database." ) 
            ) 
        ),
        'filter' => false,
        'sortable' => false,
        'is_system' => true 
    ) );
    return parent::_prepareColumns ();
  }
  /**
   * Preparing Massaction
   */
  protected function _prepareMassaction() {
    $visibility = array (
        'visible' => 'visible',
        'not visible' => 'notvisible' 
    );
    $this->setMassactionIdField ( 'id' );
    $this->getMassactionBlock ()->setFormFieldName ( 'faq_ids' );
    $this->getMassactionBlock ()->setUseSelectAll ( false );
    array_unshift ( $visibility, array (
        'label' => '',
        'value' => '' 
    ) );
    $this->getMassactionBlock ()->addItem ( 'delete', array (
        'label' => Mage::helper ( 'productfaq' )->__ ( 'Delete' ),
        'url' => $this->getUrl ( 'productfaq/adminhtml_faq/massDelete' ) 
    ) )->addItem ( 'status', array (
        'label' => Mage::helper ( 'catalog' )->__ ( 'Change Visibility' ),
        'url' => $this->getUrl ( '*/*/massVisibilty', array (
            '_current' => true 
        ) ),
        'additional' => array (
            'visibility' => array (
                'name' => 'visibility',
                'type' => 'select',
                'class' => 'required-entry',
                'label' => Mage::helper ( 'catalog' )->__ ( 'Visibility' ),
                'values' => $visibility 
            ) 
        ) 
    ) );
    return $this;
  }
  /**
   * Getting Row Url
   */
  public function getRowUrl($row) {
    return $this->getUrl ( '*/*/edit', array (
        'id' => $row->getId () 
    ) );
  }
}
