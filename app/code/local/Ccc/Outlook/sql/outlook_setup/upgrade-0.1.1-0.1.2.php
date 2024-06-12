<?php
    
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_outlook/event'))
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Event Id')
    ->addColumn('config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
    ), 'Configuration Id')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
    ), 'Group Id')
    ->addColumn('condition_on', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Condition Field')
    ->addColumn('operator', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Operator')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    ), 'Condition Value')
    ->addColumn('event', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    ), 'Event Name')
    ->setComment('Configuration Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();