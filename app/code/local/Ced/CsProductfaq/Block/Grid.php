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

class Ced_CsProductfaq_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	protected $_massactionBlockName = 'csproductfaq/widget_grid_massaction';

	/**
	 * 
	 * setting grid parameter
	 * 
	 */
	public function __construct()
    {
        parent::__construct();
        $this->setId('csfaqGrid');
        $this->setDefaultSort('page_id');
        $this->setTemplate('csmarketplace/widget/grid.phtml');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
		$this->setVarNameFilter('cms');
    }

    /**
     * 
     * Preapre Collection
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
    	$VendorId = Mage::getSingleton('customer/session')->getVendorId();
  /*      
        $collection = Mage::getModel ( 'productfaq/productfaq' )->getCollection()->addFieldToFilter('vendor_id',$VendorId);
//print_r($collection->getData());die("g");
  
        $this->setCollection($collection);

        return parent::_prepareCollection(); */



      
        $vendorsFaq = array();
        $collection = Mage::getModel('productfaq/productfaq')->getCollection()->getData();
        foreach ($collection as $Products)
        {
        	$pId = $Products['product_id'];
        	$Vendorcollection = Mage::getModel('csmarketplace/vproducts')->load($pId,'product_id')->getData();
        	//print_r($Vendorcollection);die("l");
        	if(count($Vendorcollection) > 0 && $Vendorcollection['vendor_id'] == $VendorId)
        	{
        		$vendorsFaq[] = $pId;
        	}
        }
        $Vendorcollection = Mage::getModel('csmarketplace/vproducts')->load($vendorsFaq[0],'product_id')->getData();
       
        $vendorsFaq = array_unique($vendorsFaq);
        
        $FaqCollection = Mage::getModel('productfaq/productfaq')->getCollection()->addFieldToFilter('product_id',array('in'=>$vendorsFaq));
         
        
        $this->setCollection($FaqCollection);
        
        return parent::_prepareCollection();
        
        
        
    
    
    }

    /**
     * 
     * Prepare column for grid
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
 protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('id', 
                [
            'header'    => ('Id'),
            'align'     => 'left',
            'index'     => 'id',
        ]
    );
        
       $this->addColumn('product_id', 
                [
            'header'    => ('Product Id'),
            'align'     => 'left',
            'index'     => 'product_id',
           
        ]
    );

        
        $this->addColumn('title',
                [
                'header'    => ('Title'),
                'align'     => 'left',
                'index'     => 'title'
                ]
        );
        
        
        $this->addColumn('email_id',
                [
                'header'    => ('Customer Email'),
                'align'     => 'left',
                'index'     => 'email_id'
                ]
        );
        
        $this->addColumn('visible_on_frontend',
                [
                'header' => __('Visible On Frontend'),
                'index' => 'visible_on_frontend',
                'type' => 'int',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );
        
     
        
    
   $this->addColumn('action',
        [
            'header'    => __('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'     => 'getId',
            'actions'   => array(
                array(
                    'caption' =>__('Edit'),
                    'url'     => array(
                        'base'=>'*/*/edit',
                        'params'=>array('store'=>$this->getRequest()->getParam('id'))
                    ),
                    'field'   => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
    ]);

       
        return parent::_prepareColumns();
    }
    
  
    
    /**
     * prepare mass action
     * 
     * */
  protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }
    
  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('id');
    $this->getMassactionBlock()->setFormFieldName('id');
  
    $this->getMassactionBlock()->addItem(
        'delete',
        [
        'label' => __('Delete'),
        'url' => $this->getUrl('*/*/massdelete'),
       
        ]
    );
    
 
    $this->getMassactionBlock()->addItem(
            'enable',
            [
            'label' => __('Visible'),
            'url' => $this->getUrl('*/*/massenable'),
            
            ]
    );
    
    $this->getMassactionBlock()->addItem(
            'disable',
            [
            'label' => __('Not Visible'),
            'url' => $this->getUrl('*/*/massdisable'),
        
            ]
    );
    
  }
  

	/**
     * Row click url
     *
     * @return string
     */
	public function getGridUrl()
	{
		return Mage::getUrl('*/*/grid');
	}
    
	/**
	 * getting row url
	 *
	 * @return string
	 */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
