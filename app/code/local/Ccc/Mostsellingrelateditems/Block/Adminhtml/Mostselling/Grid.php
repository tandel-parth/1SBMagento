<?php

class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('mostSellingItemsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems')->getCollection();

        $attributeCode = 'name';
        $entityType = 'catalog_product';
        $attribute = Mage::getModel('eav/config')->getAttribute($entityType, $attributeCode);
        $attributeId = $attribute->getAttributeId();

        $collection->getSelect()->join(
            array('CPEV' => 'catalog_product_entity_varchar'),
            'main_table.most_selling_product_id = CPEV.entity_id AND CPEV.attribute_id = ' . (int)$attributeId,
            array('most_selling_product_name' => 'CPEV.value')
        );
        $collection->getSelect()->join(
            array('CPEVR' => 'catalog_product_entity_varchar'),
            'main_table.related_product_id = CPEVR.entity_id AND CPEVR.attribute_id = ' . (int)$attributeId,
            array('related_product_name' => 'CPEVR.value')
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $columns = array(
            'id' => array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Primary Id'),
                'type' => 'number',
                'align' => 'right',
                'width' => '50px',
                'index' => 'id',
            ),
            'most_selling_product_name' => array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Most Selling Product Name'),
                'align' => 'left',
                'index' => 'most_selling_product_name',
                'filter_condition_callback' => array($this, '_filterMostProductName'),
            ),
            'related_product_name' => array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Related Product Name'),
                'align' => 'left',
                'index' => 'related_product_name',
                'filter_condition_callback' => array($this, '_filterRelatableProductName'),
            ),
            'created_at' => array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Created Date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'created_at',
                'renderer' => 'mostsellingrelateditems/adminhtml_mostselling_grid_renderer_datetime',
            ),
            'is_deleted' => array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Deleted'),
                'align' => 'left',
                'index' => 'is_deleted',
                'type' => 'options',
                'options' => array(
                    1 => Mage::helper('mostsellingrelateditems')->__('YES'),
                    2 => Mage::helper('mostsellingrelateditems')->__('NO'),
                ),
            ),
            'deleted_at' => array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Deleted Date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'deleted_at',
                'renderer' => 'mostsellingrelateditems/adminhtml_mostselling_grid_renderer_datetime',
            ),
            'view' =>
            array(
                'header' => Mage::helper('mostsellingrelateditems')->__('Action'),
                'align' => 'left',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('mostsellingrelateditems')->__('View'),
                        'url' => array(
                            'base' => '*/*/view',
                        ),
                        'field' => 'id',
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'view',
            )
        );

        foreach ($columns as $columnName => $columnKey) {
            $this->addColumn($columnName, $columnKey);
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    // MAss Actions 
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $delete = array(
            1 => Mage::helper('locationcheck')->__('YES'),
            2 => Mage::helper('locationcheck')->__('NO')
        );

        array_unshift($delete, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'is_deleted',
            array(
                'label' => Mage::helper('mostsellingrelateditems')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'is_deleted',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('mostsellingrelateditems')->__('Delete'),
                        'values' => $delete
                    )
                )
            )
        );

        Mage::dispatchEvent('locationcheck_adminhtml_locationcheck_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
    protected function _filterMostProductName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->getSelect()->where("CPEV.value LIKE ?", "%$value%");

        return $this;
    }
    protected function _filterRelatableProductName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("CPEVR.value LIKE ?", "%$value%");

        return $this;
    }
}
