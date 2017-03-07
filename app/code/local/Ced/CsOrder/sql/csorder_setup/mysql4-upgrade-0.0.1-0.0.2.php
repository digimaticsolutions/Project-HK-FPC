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
$installer->run("CREATE TABLE IF NOT EXISTS `{$installer->getTable('csorder/shipment')}` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
		  `shipment_id` int(11) unsigned NOT NULL ,
		  `vendor_id` int(11) unsigned NOT NULL ,
		  PRIMARY KEY (`id`), UNIQUE KEY `shipment_id` (`shipment_id`, `vendor_id`) 
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		

$installer->run("CREATE TABLE IF NOT EXISTS `{$installer->getTable('csorder/invoice')}` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
		  `invoice_id` int(11) unsigned NOT NULL ,
		  `vendor_id` int(11) unsigned NOT NULL ,
		  PRIMARY KEY (`id`), UNIQUE KEY `invoice_id` (`invoice_id`, `vendor_id`) 
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
				
$installer->endSetup();