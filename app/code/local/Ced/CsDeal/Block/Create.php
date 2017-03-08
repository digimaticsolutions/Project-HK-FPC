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
 * @package     Ced_CsDeal
 * @author      CedCommerce Magento Core Team <magentocoreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Membership list Block
 *
 * @category    Ced
 * @package     Ced_CsMembership
 * @author      CedCommerce Magento Core Team <magentocoreteam@cedcommerce.com>
 */
class Ced_CsDeal_Block_Create extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	public function getProductId()
	{
		$id=$this->getRequest()->getParam('id');
		$ids=Mage::getModel('csmarketplace/vproducts')->getVendorProductIds();
		if(in_array($id,$ids)){
			return $id;
		}
	}
	public function getProductName()
	{	$id=$this->getRequest()->getParam('id');
		$model=Mage::getModel('catalog/product')->load($id);
		$name=$this->getRequest()->getParam('name');
		if(trim($name)==trim($model->getName()))
			return $name;
	}

}