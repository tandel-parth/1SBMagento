<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('ccc_outlook/configuration');
$table = $installer->getConnection()
    ->addColumn($tableName, 'last_readed_email', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'comment' => 'Last Read Emails',
    ));
$installer->endSetup();
