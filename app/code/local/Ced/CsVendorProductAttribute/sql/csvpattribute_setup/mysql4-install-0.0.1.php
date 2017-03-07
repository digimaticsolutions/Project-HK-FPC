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
  * @package     Ced_CsVendorProductAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/* @var $installer Ced_CsVendorProductAttribute_Model_Mysql4_Setup */

$installer = $this;
$installer->startSetup ();
$installer->run("
  CREATE TABLE IF NOT EXISTS {$this->getTable('csvendorproductattribute/attribute')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vendor_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `attribute_code` varchar(100) character set utf8 NOT NULL,
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Vendor Product Attribute Set Manage';
");
$installer->endSetup ();
