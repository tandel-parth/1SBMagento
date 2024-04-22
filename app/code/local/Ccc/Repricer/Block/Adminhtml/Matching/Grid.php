<?php
class Ccc_Repricer_Block_Adminhtml_Matching_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        // Ccc_Repricer_Block_Adminhtml_Matching_Grid_Renderer_Productname::$renderedProductIds = [];
        $collection = Mage::getModel('ccc_repricer/matching')->getCollectionData();
        $columns = [
            'product_id' => 'product_id',
            'entity_type_id' => 'pro.entity_type_id',
            'attribute_id' => 'et.attribute_id',
            'product_name' => 'at.value',
            'product_sku' => 'pro.sku',
            'competitor_id' => 'competitor_id',
            'competitor_name' => 'cpev.name',
            'repricer_id' => 'repricer_id',
            'competitor_url' => 'competitor_url',
            'competitor_sku' => 'competitor_sku',
            'competitor_price' => 'competitor_price',
            'reason' => 'reason',
            'updated_date' => 'updated_date',
        ];
        $collection->getSelect()->order('product_id ASC')->reset(Zend_Db_Select::COLUMNS)
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
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_productname',
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
}

?>