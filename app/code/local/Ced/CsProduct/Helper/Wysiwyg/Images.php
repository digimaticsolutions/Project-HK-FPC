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
 * @author   CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_CsProduct_Helper_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{
	public function getStorageRoot()
	{
		$_path = '';
		$vendor = Mage::getSingleton('customer/session')->getVendor();
		if($vendor && $vid=$vendor->getId()){
			$_path = 'vendor_'.$vid.DS;
		}
			
		$module = Mage::app()->getRequest()->getModuleName();
		if($module == 'csproduct'){
			$path = Mage::getConfig()->getOptions()->getMediaDir()
			. DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY;
			$this->_storageRoot = realpath($path);
			if (!$this->_storageRoot) {
				$this->_storageRoot = $path;
			}
			$this->_storageRoot .= DS.$_path;
		} else {
			$path = Mage::getConfig()->getOptions()->getMediaDir()
			. DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY;
			$this->_storageRoot = realpath($path);
			if (!$this->_storageRoot) {
				$this->_storageRoot = $path;
			}
			$this->_storageRoot .= DS;
		}
		return $this->_storageRoot;
	}
}
?>