<?php
require_once ('../app/Mage.php'); //Path to Magento
Mage::app();
echo "<pre>";
$tmp = [];
$competitorIds = Mage::getModel('ccc_repricer/competitors')
    ->getCollection()
    ->getAllIds();

$data = [];
foreach ($competitorIds as $_competitorId) {
    $collection = Mage::getModel('catalog/product')
        ->getCollection();
    $collection->getSelect()
        ->columns('e.entity_id')
        ->joinLeft(
            ['CRM' => 'ccc_repricer_matching'],
            "e.entity_id = CRM.product_id AND CRM.competitor_id = {$_competitorId}",
            ['competitor_id']
        )
        ->where('CRM.competitor_id IS NULL')
    ;
    $columns = [
        'product_id' => 'e.entity_id',
    ];
    $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
        ->columns($columns);

    foreach ($collection->getData() as $_data) {
        $_data['competitor_id'] = $_competitorId;
        $data[] = $_data;
    }

    $tmp[$_competitorId] = $collection->getColumnValues('product_id');
    print_r($tmp[$_competitorId]);
}
$model = Mage::getSingleton('core/resource')->getConnection('core_write');
$tableName = Mage::getSingleton('core/resource')->getTableName('ccc_repricer/matching');
$result = $model->insertMultiple($tableName, $data);