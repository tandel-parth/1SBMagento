<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('ccc_filetransfer/filetransfer');
if ($installer->getConnection()->isTableExists($tableName)) {
    // Table already exists, log a message and return
    Mage::log("Table $tableName already exists", null, 'ccc_filetransfer_ftp.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_filetransfer/filetransfer'))
    ->addColumn('ftp_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'FTP Id')
    ->addColumn('filepath', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'File Path')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'File Name')
    ->addColumn('configuration_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable' => false,
    ), 'Configuration Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Created At')
    ->setComment('CCC Filetransfer FTP Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
