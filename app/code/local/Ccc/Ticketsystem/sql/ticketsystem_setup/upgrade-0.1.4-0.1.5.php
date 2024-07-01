<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_ticketsystem/comment');
$connection = $installer->getConnection();

$connection->addColumn(
    $tableName,
    'user_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'comment' => 'Comment by User'
    )
);
$connection->addColumn(
    $tableName,
    'parent_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'comment' => 'Comment Parent Id'
    )
);
$connection->addColumn(
    $tableName,
    'level',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'comment' => 'Comment Level'
    )
);

$installer->endSetup();
