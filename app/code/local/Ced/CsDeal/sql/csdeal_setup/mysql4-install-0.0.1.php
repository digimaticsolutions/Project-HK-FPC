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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('csdeal/deal')} (
  `deal_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL ,
  `days` int(10) unsigned NOT NULL ,
  `specificdays` varchar(100) NOT NULL ,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `vendor_id` int(11) unsigned NOT NULL,
  `admin_status` varchar(15) character set utf8 NOT NULL,
  `status` varchar(15) character set utf8 NOT NULL,
  `deal_price` decimal(10,2) NOT NULL,
  PRIMARY KEY  (`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Vendor Deals';
");
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('csdeal/dealsetting')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vendor_id` int(11) unsigned NOT NULL,
  `status` varchar(15) character set utf8 NOT NULL,
  `deal_list` varchar(15) character set utf8 NOT NULL,
  `timer_list` varchar(15) character set utf8 NOT NULL,
  `deal_message` varchar(250) character set utf8 NOT NULL,
  `store` int(10) unsigned NOT NULL ,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Vendor Deals';
");
$installer->endSetup();
