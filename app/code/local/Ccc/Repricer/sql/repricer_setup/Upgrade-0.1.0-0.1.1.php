<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_repricer_competitor');
$connection = $installer->getConnection();

// Modify the created_at column
$connection->modifyColumn($tableName, 'created_date', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
    'nullable' => false,
    'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    'comment'  => 'Created At'
));

// Modify the updated_at column
$connection->modifyColumn($tableName, 'updated_date', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
    'nullable' => false,
    'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
    'comment'  => 'Updated At'
));

$installer->endSetup();
