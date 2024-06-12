<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_outlook/configuration');

if ($installer->getConnection()->isTableExists($tableName)) {
    echo $tableName;
    // Table already exists, log a message and return
    Mage::log("Table $tableName already exists", null, 'ccc_outlook_configuration.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_outlook/configuration'))
    ->addColumn('configration_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Configration Id')
    ->addColumn('username', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Username')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'User Email')
    ->addColumn('api_key', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Api Key')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Api Token')
    ->setComment('CCC Outlook Configration Table');
Mage::log($table, null, 'ccc_outlook_configuration.log');
$installer->getConnection()->createTable($table);
$installer->endSetup();
