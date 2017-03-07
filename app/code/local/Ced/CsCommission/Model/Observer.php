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
  * @package     Ced_CsCommission
  * @author  	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */


/**
 * Ced Core observer
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author 		CedCommerce Core Team <connect@cedcommerce.com >
 */
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'System'.DS.'ConfigController.php'; 
class Ced_CsCommission_Model_Observer
{
    /**
     * Predispath admin action controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveCommissionConfigurationData(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			$params = Mage::app()->getRequest()->getParams();
			$params['section'] = 'ced_csmarketplace';
			$params['is_csgroup'] = 2;
			Mage::app()->getRequest()->setParams($params);
			$configuration = Mage::getControllerInstance('Mage_Adminhtml_System_ConfigController', Mage::app()->getRequest(), Mage::app()->getResponse());
			$configuration->dispatch('save');
			Mage::getSingleton('adminhtml/session')->getMessages(true);
        }
    }
}
