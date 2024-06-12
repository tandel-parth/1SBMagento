<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('ccc_outlook/attachment');
$table = $installer->getConnection()
    ->addColumn($tableName, 'email_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'comment' => 'Email Id',
    ));
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'ccc_outlook/attachment',
        'email_id',
        'ccc_outlook/email',
        'email_id'
    ),
    $tableName,
    'email_id',
    $installer->getTable('ccc_outlook/email'),
    'email_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$tableName = $installer->getTable('ccc_outlook/email');
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'ccc_outlook/email',
        'configration_id',
        'ccc_outlook/configuration',
        'configration_id'
    ),
    $tableName,
    'configration_id',
    $installer->getTable('ccc_outlook/configuration'),
    'configration_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);
$installer->endSetup();
