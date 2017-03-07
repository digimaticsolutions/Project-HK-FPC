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

/**
 * Catalog product downloadable items tab and form
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Downloadable
    extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	/**
	 * Reference to product objects that is being edited
	 *
	 * @var Mage_Catalog_Model_Product
	 */
	protected $_product = null;
	
	protected $_config = null;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		//        $this->setSkipGenerateContent(true);
		$this->setTemplate('csproduct/edit/tab/downloadable.phtml');
	}
	
	/**
	 * Get tab URL
	 *
	 * @return string
	 */
	//    public function getTabUrl()
	//    {
	//        return $this->getUrl('downloadable/product_edit/form', array('_current' => true));
	//    }
	
	/**
	 * Get tab class
	 *
	 * @return string
	 */
	//    public function getTabClass()
	//    {
	//        return 'ajax';
	//    }
	
	/**
	 * Check is readonly block
	 *
	 * @return boolean
	 */
	public function isReadonly()
	{
		return $this->getProduct()->getDownloadableReadonly();
	}
	
	/**
	 * Retrieve product
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct()
	{
		return Mage::registry('current_product');
	}
	
	/**
	 * Get tab label
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return Mage::helper('downloadable')->__('Downloadable Information');
	}
	
	/**
	 * Get tab title
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return Mage::helper('downloadable')->__('Downloadable Information');
	}
	
	/**
	 * Check if tab can be displayed
	 *
	 * @return boolean
	 */
	public function canShowTab()
	{
		return true;
	}
	
	/**
	 * Check if tab is hidden
	 *
	 * @return boolean
	 */
	public function isHidden()
	{
		return false;
	}
	
	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
		->setId('downloadableInfo');
	
		$accordion->addItem('samples', array(
				'title'   => Mage::helper('adminhtml')->__('Samples'),
				'content' => $this->getLayout()
				->createBlock('downloadable/adminhtml_catalog_product_edit_tab_downloadable_samples')
				->toHtml(),
				'open'    => false,
		));
	
		$accordion->addItem('links', array(
				'title'   => Mage::helper('adminhtml')->__('Links'),
				'content' => $this->getLayout()->createBlock(
						'downloadable/adminhtml_catalog_product_edit_tab_downloadable_links',
						'catalog.product.edit.tab.downloadable.links')
						->setTemplate('csproduct/edit/tab/downloadable/links.phtml')
						->toHtml(),
				'open'    => true,
		));
	
		$this->setChild('accordion', $accordion);
	
		return parent::_toHtml();
	}
	
}	