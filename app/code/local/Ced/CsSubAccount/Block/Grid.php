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
 * @package     Ced_CsProduct
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage products grid block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */



class Ced_CsSubAccount_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_massactionBlockName = 'csmarketplace/widget_grid_massaction';
	
	/**
	 *	Prepare Mass Action
	 */
	protected function _prepareMassaction()
	{
		
		$this->setMassactionIdField('abc');
  		$this->getMassactionBlock()->setFormFieldName('id');
  		  	
  		//$statuses = array('2'=>Mage::helper('cssubaccount')->__('Disapprove'),'1'=>Mage::helper('cssubaccount')->__('Approve'));
  		
  		$this->getMassactionBlock()->addItem('approve', array(
  			 'label'=> Mage::helper('cssubaccount')->__('Approve Sub-Vendors'),
  			 'url'  => $this->getUrl('cssubaccount/customer/approve/', array('_current'=>true)),
  			'value' =>1,
  					
  		 ));
  		$this->getMassactionBlock()->addItem('disapprove', array(
  				'label'=> Mage::helper('cssubaccount')->__('Disapprove Sub-Vendors'),
  				'url'  => $this->getUrl('cssubaccount/customer/disapprove/', array('_current'=>true)),
  				'value' =>1,
  		));
  		return $this;		
  }
	
 	public function __construct()
    { 
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setTemplate('csmarketplace/widget/grid.phtml');
        $this->setSaveParametersInSession(true);
        
    }
   
	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
	
	protected function _prepareCollection()
    {
    	$vendor = Mage::getSingleton( 'customer/session' )->getData('vendor');
    	
    	//print_r(Mage::getModel('customer/customer')->load(Mage::getSingleton( 'customer/session' )->getCustomer()->getEntityId())->getData());die;
        $collection = Mage::getModel('cssubaccount/cssubaccount')->getCollection()->addFieldToFilter('parent_vendor',$vendor->getId());
      	
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    
    protected function _prepareColumns()
    {
    	
    	$this->addColumn('first_name', array(
    			'header'    => Mage::helper('cssubaccount')->__('First Name'),
    			'index'     => 'first_name'
    	));
    	$this->addColumn('middle_name', array(
    			'header'    => Mage::helper('cssubaccount')->__('Middle Name'),
    			'index'     => 'middle_name'
    	));
    	$this->addColumn('last_name', array(
    			'header'    => Mage::helper('cssubaccount')->__('Last Name'),
    			'index'     => 'last_name'
    	));
    	$this->addColumn('email', array(
    			'header'    => Mage::helper('cssubaccount')->__('Email'),
    			'index'     => 'email'
    	));
    	$this->addColumn('status', array(
    			'header'    => Mage::helper('cssubaccount')->__('Status'),
    			'width'     => '170',
    			//'renderer'=>'Ced_CsCustomer_Block_Grid_Renderer_Available'
    			'renderer'		=>'Ced_CsSubAccount_Block_Grid_Renderer_Status'
    			
    	));
    	
    	/* $this->addColumn('subvendor_status', array(
    			'header'        => Mage::helper('cssubaccount')->__('Action'),
    			'align'         =>'left',
    			//'options'		=>array('0'=>Mage::helper('cssubaccount')->__('Pending'),'1'=>Mage::helper('cssubaccount')->__('Approved'),'2'=>Mage::helper('cssubaccount')->__('Disapprove')),
    			'renderer'		=>'Ced_CsSubVendor_Block_Grid_Renderer_Status'
    	));  */

    	$this->addExportType('*/*/exportCsv', Mage::helper('cssubaccount')->__('CSV'));
    	$this->addExportType('*/*/exportXml', Mage::helper('cssubaccount')->__('Excel XML'));
    	return parent::_prepareColumns();
    }
    
	public function getGridUrl()
	{
		return Mage::getUrl('cssubaccount/customer/grid',array(
				'store'=>$this->getRequest()->getParam('store')));
	}
	
	public function getRowUrl($row)
	{
		return Mage::getUrl('*/*/view', array(
				'store'=>$this->getRequest()->getParam('store'),
				'id'=>$row->getId())
		);
	}
}