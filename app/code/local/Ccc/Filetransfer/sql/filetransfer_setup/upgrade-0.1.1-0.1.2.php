<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('ccc_filetransfer/allpart');
if ($installer->getConnection()->isTableExists($tableName)) {
    // Table already exists, log a message and return
    Mage::log("Table $tableName already exists", null, 'ccc_allpart.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_filetransfer/allpart'))
    ->addColumn('allpart_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Allpart Id')
    ->addColumn('part_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Part Number')

    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Created At')
    ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
    ), 'Updated Date')
    ->setComment('CCC Allpart Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
