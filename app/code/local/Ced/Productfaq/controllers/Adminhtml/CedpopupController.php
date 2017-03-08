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
 * @package     Ced_Productfaq
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_Productfaq_Adminhtml_CedpopupController extends Mage_Adminhtml_Controller_Action
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