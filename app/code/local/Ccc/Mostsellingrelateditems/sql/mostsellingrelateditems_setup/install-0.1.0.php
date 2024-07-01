<?php
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_mostsellingrelateditems/mostsellingrelateditems');
if ($installer->getConnection()->isTableExists($tableName)) {
    echo $tableName;
    // Table already exists, log a message and return
    Mage::log("Table $tableName already exists", null, 'ccc_MostSelling.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_mostsellingrelateditems/mostsellingrelateditems'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Most Selling Id')
    ->addColumn('most_selling_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Most Selling Producct Id')
    ->addColumn('related_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Related Product Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Most product Created Date')
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default'  => 2,
    ), 'Deleted Status')
    ->addColumn('deleted_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => true,
    ), 'Deleted Date')
    ->setComment('CCC Most Selling Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
