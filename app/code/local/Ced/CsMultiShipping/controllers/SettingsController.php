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
 * @package     Ced_CsMultiShipping
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */  
 
class Ced_CsMultiShipping_SettingsController extends Ced_CsMarketplace_Controller_AbstractController
{
	/**
     * Default vendor account page
     */
	public function indexAction()
    {
        if(!$this->_getSession()->getVendorId()) 
        	return;
        if(!Mage::helper('csmultishipping')->isEnabled()){
        	$this->_redirect('csmarketplace/vendor/index');
        	return;
        }
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Shipping')." ".$this->__('Settings'));
        $this->renderLayout();
    }
	
	/**
	 * Save settings
	 */
	public function saveAction() {
		if(!$this->_getSession()->getVendorId()) 
			return;
		if(!Mage::helper('csmultishipping')->isEnabled()){
			$this->_redirect('csmarketplace/vendor/index');
			return;
		}
		$section = $this->getRequest()->getParam('section','');
		$groups = $this->getRequest()->getPost('groups',array());
		//print_r($groups);die;
		if(strlen($section) > 0 && $this->_getSession()->getData('vendor_id') && count($groups)>0) {
			$vendor_id = (int)$this->_getSession()->getData('vendor_id');
			try {
				Mage::helper('csmultishipping')->saveShippingData($section, $groups, $vendor_id);
				$this->_getSession()->addSuccess($this->__('The Shipping Settings has been saved.'));
				$this->_redirect('*/*/index');
				return;
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/index');
				return;
			}
		}
		$this->_redirect('*/*/index');
	}
	
}
