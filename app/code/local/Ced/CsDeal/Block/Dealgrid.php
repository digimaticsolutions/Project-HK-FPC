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
class Ced_CsDeal_Block_Dealgrid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	protected $_massactionBlockName = 'csmarketplace/widget_grid_massaction';

	public function __construct()
	{
		
		parent::__construct();
		$this->setId('deallistGrid');
		$this->setDefaultSort('entity_id');
		$this->setTemplate('csmarketplace/widget/grid.phtml');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('deallist_filter');
		
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('deal_id');
		$this->getMassactionBlock()->setFormFieldName('deal_id');
	
		$this->getMassactionBlock()->addItem('delete', array(
				'label'=> Mage::helper('catalog')->__('Delete'),
				'url'  => Mage::getUrl('*/*/massDelete',array(
				'confirm' => Mage::helper('csdeal')->__('Are you sure?')
		))
		));
		$this->getMassactionBlock()->addItem('enable', array(
				'label'=> Mage::helper('catalog')->__('Enable'),
				'url'  => Mage::getUrl('*/*/massEnable',array(
				'confirm' => Mage::helper('csdeal')->__('Are you sure?')
		))
		));
		$this->getMassactionBlock()->addItem('disable', array(
				'label'=> Mage::helper('catalog')->__('Disable'),
				'url'  => Mage::getUrl('*/*/massDisable',array(
				'confirm' => Mage::helper('csdeal')->__('Are you sure?')
		))
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
		$vendor_id=Mage::getModel('customer/session')->getVendorId();
		$collection=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('vendor_id',$vendor_id);
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}
	
	protected function _prepareColumns()
	{
		$store = $this->_getStore();
		$this->addColumn('deal_id',
				array(
						'header'=> Mage::helper('csdeal')->__('Deal ID'),
						'width' => '5px',
						'type'  => 'number',
						'align'     => 'left',
						'index' => 'deal_id',
				));
		/*$this->addColumn('product_id',
				array(
						'header'=> Mage::helper('csdeal')->__('Product Id'),
						'width' => '5px',
						'type'  => 'number',
						'align'     => 'left',
						'index' => 'product_id',
						));*/
		$this->addColumn('product_name',
				array(
						'header'=> Mage::helper('csdeal')->__('Product'),
						'width' => '150px',
						'type'  => 'number',
						'align'     => 'left',
						'index' => 'product_id',
						'renderer'=>'Ced_CsDeal_Block_Edit_Tab_Renderer_Product',
						));
		$this->addColumn('deal_price',
				array(
						'header'=> Mage::helper('csdeal')->__('Deal Price'),
						'type'  => 'price',
						'width' => '5px',
						'currency_code' => $store->getBaseCurrency()->getCode(),
						'index' => 'deal_price',
				));
		/*$this->addColumn('start_date',
				array(
						'header'=> Mage::helper('csdeal')->__('Deal Start'),
						'type' => 'datetime',
						'index' => 'start_date',
				));*/
		$this->addColumn('end_date',
				array(
						'header'=> Mage::helper('csdeal')->__('Deal End'),
						'type' => 'datetime',
						'index' => 'end_date',
				));
		$this->addColumn('status',
				array(
						'header'=> Mage::helper('csdeal')->__('Deal Status'),
						'width' => '5px',
						'type'  => 'options',
						'align'     => 'left',
						'index' => 'status',
						'options' =>Ced_CsDeal_Model_Status::getOptionArray(),

						));
		$this->addColumn('admin_status',
				array(
						'header'=> Mage::helper('csdeal')->__('Admin Status'),
						'width' => '5px',
						'type'  => 'options',
						'align'     => 'left',
						'index' => 'admin_status',
						'options' => Mage::getModel('Ced_CsDeal_Model_Deal')->getMassActionArray(),
						'renderer'=>'Ced_CsDeal_Block_Edit_Tab_Renderer_AdminStatus',
						));
	 	$this->addColumn('edit_deal',
				array(
						'header'=> Mage::helper('catalog')->__('Edit'),
						'width' => '70px',
						'index' => 'edit_deal',
						'sortable' => false,
        				'filter'   => false,
						'renderer'=>'Ced_CsDeal_Block_Edit_Tab_Renderer_Deal',
						));
		return parent::_prepareColumns();
	}
	
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid',array(
				'store'=>$this->getRequest()->getParam('store')));
	}
	public function getRowUrl($row)
	{
		return Mage::getUrl('*/*/edit', array(
				'store'=>$this->getRequest()->getParam('store'),
				'deal_id'=>$row->getDealId())
		);
	}

}
