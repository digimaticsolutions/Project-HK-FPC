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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsDeal_Block_Adminhtml_Vdeals_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
	  parent::__construct();
	  $this->setId('dealsGrid');
	  $this->setDefaultSort('deal_id');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
    }

  protected function _prepareMassaction()
  {
  	$this->setMassactionIdField('deal_id');
  	$this->getMassactionBlock()->setFormFieldName('deal_id');
 
  	$statuses = Mage::getSingleton('csdeal/deal')->getMassActionArray();
  	
  	$this->getMassactionBlock()->addItem('status', array(
  			 'label'=> Mage::helper('csdeal')->__('Approve/Disapprove'),
  			 'url'  => $this->getUrl('adminhtml/adminhtml_vdeals/massStatus/', array('_current'=>true)),
  			 'additional' => array(
  					 'visibility' => array(
  							 'name' => 'status',
  							 'type' => 'select',
  							 'class' => 'required-entry',
  							 'label' => Mage::helper('csmarketplace')->__('Approval Status'),
  					 		 'default'=>'-1',
  							 'values' =>$statuses,
  					 )
  			 )
  	 ));
  	return $this;
  }
  
  protected function _getStore()
  {
  	$storeId = (int) $this->getRequest()->getParam('store', 0);
  	return Mage::app()->getStore($storeId);
  }
  
  
   protected function _prepareCollection()
    {
		  $collection=Mage::getModel('csdeal/deal')->getCollection();
      $this->setCollection($collection);
      parent::_prepareCollection();
      return $this;
    }
 
  protected function _prepareColumns()
  {
  	    $this->addColumn('deal_id',
            array(
                'header'=> Mage::helper('csdeal')->__('Deal ID'),
                'width' => '10px',
                'type'  => 'number',
            	  'align'     => 'left',
                'index' => 'deal_id',
        ));
        $this->addColumn('product_id',
            array(
                'header'=> Mage::helper('csdeal')->__('Product ID'),
                'width' => '10px',
                'type'  => 'number',
                'align'     => 'left',
                'index' => 'product_id',
        ));
        $this->addColumn('product_name',
            array(
                'header'=> Mage::helper('csdeal')->__('Product Name'),
                'width' => '200px',
                'align'     => 'left',
                'index' => 'product_id',
                'renderer' => 'csdeal/adminhtml_vdeals_renderer_productname',
        ));
        $this->addColumn('vendor_id',
            array(
            'header'    => Mage::helper('csdeal')->__('Vendor Id'),
            'align'     => 'left',
            'width' => '10px',
            'index'     => 'vendor_id',
        ));
        $this->addColumn('vendor_id',
        		array(
        	  'header'    => Mage::helper('csdeal')->__('Vendor Name'),
  			    'align'     => 'left',
        	  'width' => '300px',
   			    'index'     => 'vendor_id',
			      'renderer' => 'csdeal/adminhtml_vdeals_renderer_vendorname',
        	  'filter_condition_callback' => array($this, '_vendornameFilter'),
        ));
        $this->addColumn('status',
            array(
            'header'    => Mage::helper('csdeal')->__('Status'),
            'align'     => 'left',
            'width' => '80px',
            'index'     => 'status',
           ));
         $store=$this->_getStore();
        $this->addColumn('product_price',
            array(
                'header'=> Mage::helper('csdeal')->__('Product Price'),
                'width' => '80px',
                'type'      =>'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'align'     => 'left',
                'index' => 'product_id',
                'renderer' => 'csdeal/adminhtml_vdeals_renderer_productprice',
        ));
       
        $this->addColumn('deal_price', array(
          'header' => Mage::helper('csdeal')->__('Deal Price'),
          'width' => '80px',
          'index' => 'deal_price',
          'type'  => 'currency',
          'type'      =>'price',
          'currency_code' => $store->getBaseCurrency()->getCode(), 
        ));
       
  	
        $this->addColumn('action',
        		array(
        				'header'    => Mage::helper('csdeal')->__('Action'),
        				'type'      => 'text',
                'width' => '120px',
        				'align'     => 'center',
        				'filter'    => false,
        				'sortable'  => false,
        				'renderer'=>'csdeal/adminhtml_vdeals_renderer_action',
        				'index'     => 'action',
        		));  	 
  	return parent::_prepareColumns();
  }
  
  protected function _vendornameFilter($collection, $column){
  	if (!$value = $column->getFilter()->getValue()) {
  		return $this;
  	}
  	$vendorIds = 	Mage::getModel('csmarketplace/vendor')->getCollection()
  	->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'))
  	->getAllIds();
  
  	if(count($vendorIds)>0)
  		$this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
  	else{
  		$this->getCollection()->addFieldToFilter('vendor_id');
  	}
  	return $this;
  }
  
  public function getGridUrl() {
  	return $this->getUrl('*/adminhtml_vdeals/grid', array('_secure'=>true, '_current'=>true));	
  }
}