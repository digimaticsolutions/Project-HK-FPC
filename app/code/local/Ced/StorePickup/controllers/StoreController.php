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
class Ced_StorePickup_StoreController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout ();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Stores Pickups'));
		$this->renderLayout ();
	}
	public function searchAction()
	{
		
		$post_data = $this->getRequest()->getPost();
		//print_r($post_data);die("jbk");
		$this->loadLayout ();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Search Stores'));
		$this->renderLayout ();
	}
}