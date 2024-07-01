<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_ticketsystem/filter');

if ($installer->getConnection()->isTableExists($tableName)) {
    Mage::log("Table $tableName already exists", null, 'ccc_filter.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_ticketsystem/filter'))
    ->addColumn('filter_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Filter Id')
    ->addColumn('filter_data', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Filter Data')
    ->addColumn('filter_name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Filter name')
    ->setComment('FIlter Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
