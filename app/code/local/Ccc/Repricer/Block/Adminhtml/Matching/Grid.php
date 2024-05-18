<?php

use function PHPSTORM_META\type;

class Ccc_Repricer_Block_Adminhtml_Matching_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_massactionBlockName = 'repricer/adminhtml_matching_massaction';
    public function __construct()
    {
        parent::__construct();
        $this->setId('matchingGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setTemplate('repricer/matching/grid.phtml');

    }
    protected function _prepareCollection()
    {
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
            'updated_date' => 'main_table.updated_date',
            'pc_combine' => new Zend_Db_Expr('GROUP_CONCAT(CONCAT(product_id,"-",cpev.competitor_id) ORDER BY cpev.competitor_id SEPARATOR ",")'),
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
            'pc_combine' =>
            array(
                'header' => Mage::helper('repricer')->__('PC ID'),
                'align' => 'center',
                'width' => '50px',
                'index' => 'pc_combine',
                'column_css_class' => 'pc_combine',
                'header_css_class' => 'pc_combine',
                'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
            ),
            'competitor_name' =>
            array(
                'header' => Mage::helper('repricer')->__('Competitor Name'),
                'align' => 'left',
                'index' => 'competitor_name',
                'type' => 'options',
                'options' => Mage::getModel('ccc_repricer/competitors')->getCompetitorArray(),
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
                'type' => 'number',
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
                'type' => 'date',
                'index' => 'updated_date',
                'renderer' => 'repricer/adminhtml_matching_grid_renderer_competitordata',
                'filter_condition_callback' => array($this, '_filterUpdateDateCondition'),
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
                        'field' => 'repricer_id',
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
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('pc_combine');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('pc_combine');
        $reasonArr = Mage::getModel('ccc_repricer/matching')->getReasons();

        array_unshift($reasonArr, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'reason',
            array(
                'label' => Mage::helper('repricer')->__('Change Reason'),
                'url' => $this->getUrl('*/*/massReason', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'reason',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('repricer')->__('Reason'),
                        'values' => $reasonArr
                    )
                )
            )
        );

        return parent::_prepareMassaction();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
    protected function _filterProductName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->getSelect()->where("at.value LIKE '%$value%' OR pro.sku LIKE '%$value%'");

        return $this;
    }
    protected function _filterCompetitorName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("main_table.competitor_id LIKE ?", "%$value%");

        return $this;
    }
    protected function _filterUpdateDateCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        if (isset($value['orig_from'])) {
            $value['orig_from'] = date('Y-m-d 00:00:00', strtotime($value['orig_from']));
            $collection->addFieldToFilter('main_table.updated_date', array('from' => $value['orig_from'], 'datetime' => true));
        }
        if (isset($value['orig_to'])) {
            $value['orig_to'] = date('Y-m-d 23:59:59', strtotime($value['orig_to']));
            $collection->addFieldToFilter('main_table.updated_date', array('to' => $value['orig_to'], 'datetime' => true));
        }
    }
}
