<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_repricer/competitors');
$connection = $installer->getConnection();

// Modify the created_at column
$connection->modifyColumn(
    $tableName,
    'created_date',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        'comment' => 'Created Date'
    )
);

// Modify the updated_at column
$connection->modifyColumn(
    $tableName,
    'updated_date',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
        'comment' => 'Updated Date'
    )
);

$tableName = $installer->getTable('ccc_repricer/matching');
if ($installer->getConnection()->isTableExists($tableName)) {
    Mage::log("Table $tableName already exists", null, 'ccc_repricer_matching.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_repricer_matching'))
    ->addColumn('repricer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Repricer Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Product ID')
    ->addColumn('competitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Competitor Id')
    ->addColumn('competitor_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'competitor url')
    ->addColumn('competitor_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'competitor SKU')
    ->addColumn('competitor_price', Varien_Db_Ddl_Table::TYPE_FLOAT, 255, array(
    ), 'Competitor Price')
    ->addColumn('reason', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'default' => '0',
    ), 'reason')
    ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
    ), 'Updated Date ')
    ->setComment('CCC Repricer Matching Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();