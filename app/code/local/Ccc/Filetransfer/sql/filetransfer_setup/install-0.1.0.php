<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('ccc_filetransfer/configuration');
if ($installer->getConnection()->isTableExists($tableName)) {
    // Table already exists, log a message and return
    Mage::log("Table $tableName already exists", null, 'ccc_filetransfer.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_filetransfer/configuration'))
    ->addColumn('configuration_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Configuration Id')
    ->addColumn('username', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'User Name')
    ->addColumn('password', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'User Password')
    ->addColumn('host', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Host')
    ->addColumn('port', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable' => false,
    ), 'Port')
    ->setComment('CCC Filetransfer Configuration Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
