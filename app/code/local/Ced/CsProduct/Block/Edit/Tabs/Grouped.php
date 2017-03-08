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
 * edit tabs for grouped product
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsProduct_Block_Edit_Tabs_Grouped extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('csmarketplace/widget/tabs.phtml');
	}
	
	/**
	 * Preparing global layout
	 *
	 * You can redefine this method in child classes for changin layout
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('super', array(
            'label'     => Mage::helper('catalog')->__('Associated Products'),
            'url'       => $this->getUrl('*/*/superGroup', array('_current'=>true)),
            'class'     => 'ajax',
        ));
    }
}
