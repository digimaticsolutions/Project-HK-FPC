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
 * @category    Ced;
 * @package     Ced_CsOrder 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->run("ALTER TABLE `{$installer->getTable('csorder/invoice')}`  ADD `invoice_order_id` int(11) unsigned NOT NULL,
  ADD `base_shipping_amount` float,
  ADD `shipping_amount` float,
  ADD `shipping_code` varchar(1000),
  ADD `shipping_description` varchar(1000)");
$installer->endSetup();