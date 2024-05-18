<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_jethalal/jalebi');
$connection = $installer->getConnection();

// Modify the created_at column
$connection->addColumn(
    $tableName,
    'jalebi_name',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        'nullable' => false,
        'comment' => 'Jalebi Name'
    )
);

$installer->endSetup();
    