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
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('storepickup/storepickup'))
    ->addColumn('pickup_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'pickup_id')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'store_name')
    ->addColumn('store_manager_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'store_manager_name')
    ->addColumn('store_manager_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'store_manager_email')
   ->addColumn('store_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'store_address')
    ->addColumn('store_city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    ), 'store_city')
     ->addColumn('store_country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25500, array(
   ), 'store_country')
   ->addColumn('store_state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'store_state')
   ->addColumn('store_zcode', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
   ), 'store_zcode')
   ->addColumn('latitude', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'latitude')
   ->addColumn('longitude', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'longitude')
   ->addColumn('store_phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'store_phone')
   ->addColumn('latitude', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'latitude')
   ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 255, array(
   ), 'created_at')
   ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 255, array(
   ), 'update_time')
   ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
   ), 'is_active')
   ->addColumn('shipping_price', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'shipping_price')
   ->addColumn('days', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'days')
   ->addColumn('start', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'start')
   ->addColumn('end', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'end')
   ->addColumn('interval', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
   ), 'interval')
   ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
   ), 'status')
   ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
   ), 'vendor_id');
   
 $installer->getConnection()->createTable($table);
$installer->endSetup();

 

