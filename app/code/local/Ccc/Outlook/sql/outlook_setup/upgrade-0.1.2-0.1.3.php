<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_outlook/configuration');
$connection = $installer->getConnection();

// Modify the created_at column
$connection->addColumn(
    $tableName,
    'redirect_url',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        'nullable' => false,
        'comment' => 'User Redirect Url'
    )
);
$connection->addColumn(
    $tableName,
    'client_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        'nullable' => false,
        'comment' => 'User Client ID'
    )
);
$connection->addColumn(
    $tableName,
    'client_secret',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        'nullable' => false,
        'comment' => 'User Client Secret'
    )
);
$connection->addColumn(
    $tableName,
    'scope',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        'nullable' => false,
        'comment' => 'User scope'
    )
);
$connection->addColumn(
    $tableName,
    'is_active',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 255,
        'nullable' => false,
        'comment' => 'Is Active',
        'default' => '2',
    )
);
$connection->dropColumn($tableName, 'api_key');
$connection->dropColumn($tableName, 'token');
$connection->dropColumn($tableName, 'password');    


$installer->endSetup();