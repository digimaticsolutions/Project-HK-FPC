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
  * @package     Ced_Productfaq
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
$installer = $this;
$installer->startSetup ();
$table = $installer->getConnection()->newTable ( $installer->getTable('productfaq/productfaq'))->addColumn( 'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true 
), 'id' )->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array (), 'product_id' )->addColumn( 'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 2055, array (), 'title' )->addColumn( 'description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 2055, array (), 'description' )->addColumn ( 'email_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 2055, array (), 'email_id' )->addColumn( 'visible_on_frontend', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array (), 'visible_on_frontend' );

$installer->getConnection()->createTable ( $table );

$table = $installer->getConnection()->newTable($installer->getTable('productfaq/like'))->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true 
), 'id' )->addColumn( 'question_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, null, array (
    'nullable' => false 
), 'question_id' )->addColumn ('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, null, array (
    'nullable' => false 
), 'product_id' )->addColumn('user_ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 555, null, array (
    'nullable' => false 
), 'user_ip' )->addColumn('likes', Varien_Db_Ddl_Table::TYPE_INTEGER, 555, null, array (
    'nullable' => false 
), 'likes' );
$installer->getConnection()->createTable ( $table );
$installer->endSetup ();

