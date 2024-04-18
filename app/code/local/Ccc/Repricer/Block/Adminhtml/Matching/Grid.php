<?php
class Ccc_Repricer_Block_Adminhtml_Matching_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_repricer/matching')->getCollection();

        // Add a limit to the number of products fetched
        // $collection->setPageSize(50); // Adjust the limit as needed
        $collection->getSelect()
            ->join(
                array('cpev' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
                'cpev.competitor_id = main_table.competitor_id',
                ['']
            );
        // Add additional join
        $collection->getSelect()
            ->join(
                array('pro' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
                'main_table.product_id = pro.entity_id',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('et' => Mage::getSingleton('core/resource')->getTableName('eav/attribute')),
                'et.entity_type_id = pro.entity_type_id && et.attribute_code = "name"',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('at' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar')),
                'at.attribute_id = et.attribute_id AND product_id = at.entity_id AND at.store_id = 0',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('sta' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_int')),
                'sta.entity_id = pro.entity_id AND sta.attribute_id = 96 AND sta.value = 1 AND sta.store_id = 0',
                ['']
            );
        // Reset columns and set your desired columns
        $columns = [
            'product_id' => 'product_id',
            'entity_type_id' => 'pro.entity_type_id',
            'attribute_id' => 'et.attribute_id',
            'product_name' => 'at.value',
            'competitor_id' => 'competitor_id',
            'competitor_name' => 'cpev.name',
            'repricer_id' => 'repricer_id',
            'competitor_url' => 'competitor_url',
            'competitor_sku' => 'competitor_sku',
            'competitor_price' => 'competitor_price',
            'reason' => 'reason',
            'updated_date' => 'updated_date',
        ];

        $select = $collection->getSelect()->order('repricer_id ASC')->reset(Zend_Db_Select::COLUMNS)
            ->columns($columns);
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
                    'filter_condition_callback' => array($this, '_filterProductName'),
                ),
            'competitor_name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Name'),
                    'align' => 'left',
                    'index' => 'competitor_name',
                    'filindexter_condition_callback' => array($this, '_filterCompetitorName'),
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
                    'options' => Mage::getModel('ccc_repricer/matching')->getReason(),
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


        $this->addExportType('*/*/exportCsv', Mage::helper('repricer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('repricer')->__('XML'));

        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('repricer_id' => $row->getId()));
    }
    protected function _filterProductName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()->where("at.value LIKE ?", "%$value%");

        return $this;
    }

    protected function _filterCompetitorName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()->where("cpev.name LIKE ?", "%$value%");

        return $this;
    }
    public function getCsv()
    {
        // Get the collection without pagination
        $collection = Mage::getModel('ccc_repricer/matching')->getCollection();
        $collection->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
        $collection->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);

        // Modify the SQL query to include the competitor name from the joined table
        $select = $collection->getSelect();
        $select->joinLeft(
            array('rc' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
            'rc.competitor_id = main_table.competitor_id',
            array('competitor_name' => 'rc.name')
        );

        $data = [];
        $columns = $this->_getExportColumns();

        // Add header row
        $header = [];
        foreach ($columns as $column) {
            // Adjust the header labels as needed
            switch ($column) {
                case 'repricer_id':
                    $header[] = $this->__('Repricer ID');
                    break;
                case 'product_id':
                    $header[] = $this->__('Product ID');
                    break;
                case 'competitor_name':
                    $header[] = $this->__('Competitor Name');
                    break;
                case 'competitor_url':
                    $header[] = $this->__('Competitor URL');
                    break;
                case 'competitor_sku':
                    $header[] = $this->__('Competitor SKU');
                    break;
                case 'competitor_price':
                    $header[] = $this->__('Competitor Price');
                    break;
            }
        }
        $data[] = $header;

        // Add data rows
        foreach ($collection as $item) {
            $row = [];
            foreach ($columns as $column) {
                // Get data from the modified SQL query
                if ($column === 'competitor_name') {
                    $row[] = $item->getCompetitorName();
                } else {
                    $row[] = $item->getData($column);
                }
            }
            $data[] = $row;
        }

        // Generate CSV content
        $csv = '';
        foreach ($data as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }
        return $csv;
    }

    protected function _getExportColumns()
    {
        return ['product_id', 'competitor_name', 'competitor_url', 'competitor_sku'];
    }
}

?>