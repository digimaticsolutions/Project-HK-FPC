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
  * @package     Ced_CsCmsPage
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/* @var $installer Ced_CsMarketplace_Model_Entity_Setup */

	$installer = $this;
	$installer->startSetup();
	$installer->run("CREATE TABLE IF NOT EXISTS `{$this->getTable('cscmspage/cmspage')}` (
					`page_id` int(11) NOT NULL auto_increment,
					`title` varchar(255) NOT NULL default '',
					`root_template` varchar(255) NOT NULL default '',
					`meta_keywords` text NOT NULL default '',
					`meta_description` text NOT NULL default '',
					`identifier` varchar(255) NOT NULL default '',
					`content_heading` varchar(255) NOT NULL default '',
					`content` text NOT NULL default '',
					`creation_time` datetime NULL,
					`update_time` datetime NULL,
					`is_active` tinyint(1) NOT NULL default '0',
					`is_approve` tinyint(1) NOT NULL default '0',
					`sort_order` varchar(255) NOT NULL default '',
					`layout_update_xml` text NOT NULL default '',
					`vendor_id` int(11) NOT NULL default '0',
							
					PRIMARY KEY  (`page_id`)
					);	
				
					CREATE TABLE IF NOT EXISTS `{$this->getTable('cscmspage/vendorcms')}` (
					`page_id` int(11) NOT NULL default '0',
					`store_id` int(11) NOT NULL default '0',
					`id` int(11) NOT NULL auto_increment,
					`vendor_id` int(11) NOT NULL default '0',
					`is_home` tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (`id`)
					
					);
					
					CREATE TABLE IF NOT EXISTS `{$this->getTable('cscmspage/block')}` (
					`block_id` int(11) NOT NULL auto_increment,
					`vendor_id` int(11) NOT NULL default '0',
					`title` varchar(255) NOT NULL default '',
					`identifier` varchar(255) NOT NULL default '',
					`content` text NOT NULL default '',
					`creation_time` datetime NULL,
					`update_time` datetime NULL,
					`is_active` tinyint(1) NOT NULL default '0',
					`is_approve` tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (`block_id`)
					);	
			
					CREATE TABLE IF NOT EXISTS `{$this->getTable('cscmspage/vendorblock')}` (
					`block_id` int(11) NOT NULL default '0',
					`store_id` int(11) NOT NULL default '0',
					`id` int(11) NOT NULL auto_increment,
					`vendor_id` int(11) NOT NULL default '0',
					PRIMARY KEY  (`id`)
					
					);
				
");
	
	$installer->getConnection()->addConstraint('FK_vcms_page_id_RELATION_vendorcmsstore_page_id',
										        $installer->getTable('cscmspage/vendorcms'), 
										        'page_id',
										        $installer->getTable('cscmspage/cmspage'), 
										        'page_id',
										        'cascade', 
										        'cascade'
											);
	
	$installer->getConnection()->addConstraint('FK_vblock_page_id_RELATION_vendorblockstore_block_id',
										        $installer->getTable('cscmspage/vendorblock'), 
										        'block_id',
										        $installer->getTable('cscmspage/block'), 
										        'block_id',
										        'cascade', 
										        'cascade'
											);

$installer->endSetup();

?>