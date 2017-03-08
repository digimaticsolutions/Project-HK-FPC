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
 * @package     Ced_StorePickup
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_StorePickup_Adminhtml_CedpopupController extends Mage_Adminhtml_Controller_Action
{
	public function cedpopAction() {
	
		if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
			$this->_redirect('*/index/login');
			return;
		}
		$this->loadLayout(array('c_e_d_c_o_m_m_e_r_c_e_2'));
		$this->renderLayout();
	}
	
	
	
}