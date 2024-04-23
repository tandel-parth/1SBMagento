<?php
class Ccc_Repricer_Block_Adminhtml_Matching_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    // public function __construct(){
    //     $this->setTemplate('repricer/matching/grid.phtml');
    // }
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
        $collection->getSelect()->order('product_id')->group('product_id')->reset(Zend_Db_Select::COLUMNS)
            ->columns($columns);
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'product_name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Product Name'),
                    'align' => 'left',
                    'width' => '400px',
                    'index' => 'product_name',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_productname',
                    'filter_condition_callback' => array($this, '_filterProductName'),
                ),
            'competitor_name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Name'),
                    'align' => 'left',
                    'index' => 'competitor_name',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                    'filter_condition_callback' => array($this, '_filterCompetitorName'),
                ),
            'competitor_url' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor URL'),
                    'align' => 'left',
                    'index' => 'competitor_url',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                ),
            'competitor_sku' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Product SKU'),
                    'align' => 'left',
                    'index' => 'competitor_sku',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                ),
            'competitor_price' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Product Price'),
                    'align' => 'left',
                    'index' => 'competitor_price',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                ),
            'reason' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor reason'),
                    'align' => 'left',
                    'index' => 'reason',
                    'type' => 'options',
                    'options' => Mage::getModel('ccc_repricer/matching')->getReasons(),
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                ),
            'updated_date' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Updated Date'),
                    'align' => 'left',
                    'type' => 'datetime',
                    'index' => 'updated_date',
                    // 'renderer' => 'repricer/adminhtml_matching_grid_renderer_datetime',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                ),
                'edit' =>
                array(
                    'header' => Mage::helper('repricer')->__('Action'),
                    'align' => 'left',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('repricer')->__('Edit'),
                            'url' => array(
                                'base' => '*/*/edit',
                            ),
                            'field' => 'repricer_id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'edit',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                )
        );

        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    // public function getRowUrl($row)
    // {
    //     return $this->getUrl('*/*/edit', array('repricer_id' => $row->getId()));
    // }
    // public function getGridUrl()
    // {
    //     return $this->getUrl('*/*/grid', array('_current' => true));
    // }
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