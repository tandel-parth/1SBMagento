<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('ccc_outlook/configuration');
$table = $installer->getConnection()
    ->addColumn($tableName, 'last_readed_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'comment' => 'Last Read Id',
    ));
    $installer->getConnection()->addForeignKey(
        $installer->getFkName(
            'ccc_outlook/configuration',
            'last_readed_id',
            'ccc_outlook/email',
            'email_id'
        ),
        $tableName,
        'last_readed_id',
        $installer->getTable('ccc_outlook/email'),
        'email_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->endSetup();
