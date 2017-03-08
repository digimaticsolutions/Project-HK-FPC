
<!--
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
* @package     Ced_CsSubAccount
* @author      CedCommerce Core Team <connect@cedcommerce.com >
* @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
-->
<?php

$installer = $this;

$installer->startSetup();
/**
 * installer query to setup database table at the time of module installation
*/
$installer->run("

		-- DROP TABLE IF EXISTS {$this->getTable('ced_subvendor')};
		CREATE TABLE {$this->getTable('ced_subvendor')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`parent_vendor` int(11) DEFAULT NULL,
		`first_name` varchar(20) DEFAULT NULL,
		`last_name` varchar(20) DEFAULT NULL,
		`middle_name` varchar(20) DEFAULT NULL,
		`email` varchar(40) DEFAULT NULL,
		`password` varchar(40) DEFAULT NULL,
		`status` tinyint(1)  DEFAULT NULL COMMENT '2= Disapprove, 1= Approve, 0= New',
		`role` varchar(250) DEFAULT NULL,
		`created_at` TIMESTAMP,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	-- DROP TABLE IF EXISTS {$this->getTable('ced_subaccount_status')};
		CREATE TABLE {$this->getTable('ced_subaccount_status')} (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`parent_vendor` int(11) DEFAULT NULL,
		`email` varchar(50) DEFAULT NULL,
		`status` tinyint(1)  DEFAULT NULL COMMENT '2= Rejected, 1= Accepted, 0= Pending',
		`created_at` TIMESTAMP,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();