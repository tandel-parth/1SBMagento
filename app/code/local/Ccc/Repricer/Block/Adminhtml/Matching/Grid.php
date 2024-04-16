<?php
class Ccc_Repricer_Block_Adminhtml_Matching_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();

        $collection->addAttributeToSelect('name', 'outer');
        $collection->addAttributeToFilter('status', 1);
        // Add a limit to the number of products fetched
        // $collection->setPageSize(50); // Adjust the limit as needed
        $collection->getSelect()
            ->join(
                array('cpev' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
                '',
                ['']
            );

        // Add additional join
        $collection->getSelect()
            ->join(
                array('map' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/matching')),
                'map.product_id = e.entity_id && map.competitor_id = cpev.competitor_id',
                ['']
            );

        // Reset columns and set your desired columns
        $columns = [
            'product_id' => 'e.entity_id',
            'product_name' => 'at_name.value',
            'competitor_id' => 'cpev.competitor_id',
            'competitor_name' => 'cpev.name',
            'repricer_id' => 'map.repricer_id',
            'competitor_url' => 'map.competitor_url',
            'competitor_sku' => 'map.competitor_url',
            'competitor_price' => 'map.competitor_price',
            'reason' => 'map.reason',
            'updated_date' => 'map.updated_date',
        ];

        $select = $collection->getSelect();
        $select
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns($columns);
        // echo "<pre>";
        // print_r($collection->getData());
        // die();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'repricer_id' =>
                array(
                    'header' => Mage::helper('repricer')->__('Repricer ID'),
                    'type' => 'number',
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'repricer_id',
                ),
            'product_name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Product Name'),
                    'align' => 'left',
                    'index' => 'product_name',
                ),
            'competitor_name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Name'),
                    'align' => 'left',
                    'index' => 'competitor_name',
                ),

            'competitor_url' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor URL'),
                    'align' => 'left',
                    'index' => 'competitor_url',
                ),



            'competitor_sku' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Product SKU'),
                    'align' => 'left',
                    'index' => 'competitor_sku',
                ),
            'competitor_price' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Product Price'),
                    'align' => 'left',
                    'index' => 'competitor_price',
                ),
            'reason' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor reason'),
                    'align' => 'left',
                    'index' => 'reason',
                    'type' => 'options',
                    'options' => array(
                        '0' => 'no match',
                        '1' => 'active',
                        '2' => 'out of stock',
                        '3' => 'not available',
                        '4' => 'rong match'
                    ),
                ),
            'updated_date' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Updated Date'),
                    'align' => 'left',
                    'type' => 'datetime',
                    'index' => 'updated_date',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_datetime',
                )
        );

        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('repricer_id' => $row->getId()));
    }
}