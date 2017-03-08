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
 * @package     Ced_CsTransaction 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Ced_CsTransaction_Model_Entity_Setup */
$installer = $this;

/* $installer->run("CREATE TABLE IF NOT EXISTS `{$installer->getTable('cstransaction/vorder_items')}` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
		  `order_item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Item Id',
		  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Id',
		  `order_increment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Increment Id',
		  `vendor_id` int(11) DEFAULT NULL COMMENT 'Vendor ID',
		  `qty_ready_to_pay` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Ready To Pay',
		  `qty_paid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Paid',
		  `amount_paid` decimal(10,4) unsigned NOT NULL DEFAULT '0' COMMENT 'Amount Paid',
		  `qty_ready_to_refund` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Ready To Refund',
		  `qty_refunded` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Refunded',
		  `amount_refunded` decimal(10,4) unsigned NOT NULL DEFAULT '0' COMMENT 'Amount Refunded',
		  `currency` varchar(10) NOT NULL COMMENT 'Currency',
		  `base_row_total` decimal(10,4) NOT NULL COMMENT 'Base Row Total',
		  `row_total` decimal(10,4) DEFAULT NULL COMMENT 'Row Total',
		  `item_fee` decimal(10,4) DEFAULT NULL COMMENT 'Item Fee',
		  `shop_commission_type_id` text NOT NULL COMMENT 'Shop Commission Type',
		  `shop_commission_rate` decimal(10,4) NOT NULL COMMENT 'Shop Commission Rate',
		  `shop_commission_base_fee` decimal(10,4) NOT NULL COMMENT 'Shop Commission Base Fee',
		  `shop_commission_fee` decimal(10,4) NOT NULL COMMENT 'Shop Commission Fee',
		  `product_qty` float NOT NULL COMMENT 'Product Qty',
		  `item_payment_state` varchar(11) NOT NULL COMMENT 'Item Payment State',
		  `billing_country_code` varchar(100) NOT NULL COMMENT 'Billing Country Code',
		  `shipping_country_code` varchar(100) NOT NULL COMMENT 'Shipping Country Code',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Vorder Items' AUTO_INCREMENT=1 ;"); */
$installer->run("CREATE TABLE IF NOT EXISTS `{$installer->getTable('cstransaction/vorder_items')}` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
		  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent Id',
		  `order_item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Item Id',
		  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Id',
		  `order_increment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Increment Id',
		  `vendor_id` int(11) DEFAULT NULL COMMENT 'Vendor ID',
		  `qty_ready_to_pay` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Ready To Pay',
		  `qty_paid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Paid',
		  `total_invoiced_amount` decimal(10,4) unsigned NOT NULL DEFAULT '0' COMMENT 'Total invoiced amount',
		  `total_creditmemo_amount` decimal(10,4) unsigned NOT NULL DEFAULT '0' COMMENT 'Total CreditMemo amount',
		  `amount_paid` decimal(10,4) unsigned NOT NULL DEFAULT '0' COMMENT 'Amount Paid',
		  `qty_ready_to_refund` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Ready To Refund',
		  `qty_pending_to_refund` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Pending To Refund',
		  `qty_refunded` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity Refunded',
		  `amount_refunded` decimal(10,4) unsigned NOT NULL DEFAULT '0' COMMENT 'Amount Refunded',
		  `currency` varchar(10) NOT NULL COMMENT 'Currency',
		  `sku` varchar(20) NOT NULL COMMENT 'Sku',
		  `base_row_total` decimal(10,4) NOT NULL COMMENT 'Base Row Total',
		  `row_total` decimal(10,4) DEFAULT NULL COMMENT 'Row Total',
		  `item_fee` decimal(10,4) DEFAULT NULL COMMENT 'Item Fee',
		  `item_commission` decimal(10,4) DEFAULT NULL COMMENT 'Item Commission',
		  `shop_commission_type_id` text NOT NULL COMMENT 'Shop Commission Type',
		  `shop_commission_rate` decimal(10,4) NOT NULL COMMENT 'Shop Commission Rate',
		  `shop_commission_base_fee` decimal(10,4) NOT NULL COMMENT 'Shop Commission Base Fee',
		  `shop_commission_fee` decimal(10,4) NOT NULL COMMENT 'Shop Commission Fee',
		  `product_qty` float NOT NULL COMMENT 'Product Qty',
		  `item_payment_state` varchar(11) NOT NULL COMMENT 'Item Payment State',
		  `billing_country_code` varchar(100) NOT NULL COMMENT 'Billing Country Code',
		  `shipping_country_code` varchar(100) NOT NULL COMMENT 'Shipping Country Code',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Vorder Items' AUTO_INCREMENT=1 ;");
		
		
	if(version_compare(Mage::getVersion(), '1.6', '<=')) { 	
		$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vpayment'), 'item_wise_amount_desc', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(9000)');
		$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vpayment'), 'total_shipping_amount', Varien_Db_Ddl_Table::TYPE_DOUBLE);
		
	} 
	else {
		$installer->getConnection()
			->addColumn($installer->getTable('csmarketplace/vpayment'),
				'item_wise_amount_desc',
				array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'comment' => 'item_wise_amount_desc'
				)
			);
		$installer->getConnection()
			->addColumn($installer->getTable('csmarketplace/vpayment'),
				'total_shipping_amount',
				array(
					'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
					'nullable' => true,
					'default' => 0,
					'comment' => 'total_shipping_amount'
				)
			);
	}
$installer->endSetup();