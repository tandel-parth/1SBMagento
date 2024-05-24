<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('sales/order');
$connection = $installer->getConnection();

// Modify the created_at column
$connection->addColumn(
    $tableName,
    'is_location_checked',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'default' => '2',
        'comment' => 'Location checked'
    )
);
$connection->addColumn(
    $tableName,
    'product_excluded_location_check',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'default' => '2',
        'comment' => 'Product Location checked'
    )
);

$installer->endSetup();