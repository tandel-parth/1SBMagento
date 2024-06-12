<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_outlook/configuration');
$connection = $installer->getConnection();

// Modify the created_at column
$connection->addColumn(
    $tableName,
    'password',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        'nullable' => false,
        'comment' => 'User password'
    )
);

$installer->endSetup();
    