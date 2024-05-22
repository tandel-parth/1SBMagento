<?php
class Ccc_Jethalal_Block_Adminhtml_Jalebi_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('jalebiGrid');
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_jethalal/jalebi')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'jalebi_id' =>
            array(
                'header' => Mage::helper('jethalal')->__('Jalebi Id'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'jalebi_id',
                'column_css_class' => 'jalebi_id',
            ),

            'jalebi_type' =>
            array(
                'header' => Mage::helper('jethalal')->__('Jalebi Type'),
                'align' => 'left',
                'index' => 'jalebi_type',
                'type' => 'text',
                'column_css_class' => 'jalebi_type',
            ),

            'status' =>
            array(
                'header' => Mage::helper('jethalal')->__('Jalebi Status'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'status',
                'type' => 'options',
                'options' => array(
                    "1" => "Enabled",
                    "2" => "Disabled",
                ),
                'column_css_class' => 'status',
            ),

            'created_date' =>
            array(
                'header' => Mage::helper('jethalal')->__('Jalebi Created Date'),
                'align' => 'left',
                'width' => '200px',
                'type' => 'datetime',
                'index' => 'created_date',
                'renderer' => 'jethalal/adminhtml_jalebi_grid_renderer_datetime',

            ),
            'updated_date' =>
            array(
                'header' => Mage::helper('jethalal')->__('Jalebi Updated Date'),
                'align' => 'left',
                'width' => '200px',
                'type' => 'datetime',
                'index' => 'updated_date',
                'renderer' => 'jethalal/adminhtml_jalebi_grid_renderer_datetime',
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
                'renderer' => 'jethalal/adminhtml_jalebi_grid_renderer_editbutton',
            )
        );
        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('jalebi_id');
        $this->getMassactionBlock()->setFormFieldName('jalebi_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('jethalal')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('jethalal')->__('Are you sure you want to delete selected Jalebi?')
            )
        );

        $statuses = array(
            "1" => "Enabled",
            "2" => "Disabled",
        );

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('jethalal')->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('jethalal')->__('Status'),
                        'values' => $statuses
                    )
                )
            )
        );

        Mage::dispatchEvent('jethalal_adminhtml_jalebi_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
    public function getRowClass(Varien_Object $row)
    {
        $primaryKey = $row->getId(); // Assuming 'jalebi_id' is the primary key
        return 'editable-' . $primaryKey;
    }
}
