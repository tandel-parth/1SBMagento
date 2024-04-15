<?php
class Ccc_Repricer_Block_Adminhtml_Competitors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct(){
        $this->setTemplate('repricer/competitors/grid.phtml');
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_repricer/competitors')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'competitor_id' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Id'),
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'competitor_id',
                ),

            'name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Name'),
                    'align' => 'left',
                    'index' => 'name',
                ),

            'url' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor URL'),
                    'align' => 'left',
                    'index' => 'url',
                ),

            'status' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Status'),
                    'align' => 'left',
                    'index' => 'status',
                ),

            'filename' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor File Name'),
                    'align' => 'left',
                    'index' => 'filename',
                ),

                'created_date' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Created Date'),
                    'align' => 'left',
                    'index' => 'created_date',
                    // 'type' => 'datetime',
                    // 'renderer' => 'repricer/adminhtml_competitors_grid_renderer_datetime',
                ),
                'updated_date' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Updated Date'),
                    'align' => 'left',
                    'index' => 'updated_date',
                    // 'type' => 'datetime',
                    'renderer' => 'repricer/adminhtml_competitors_grid_renderer_datetime',
                )
        );

        foreach ($collumn as $collumnName => $collumnKey) {
                $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    // MAss Actions 
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('competitor_id');
        $this->getMassactionBlock()->setFormFieldName('competitor_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('repricer')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('repricer')->__('Are you sure you want to delete selected competitors?')
            )
        );

        $statuses = Mage::getSingleton('ccc_repricer/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('repricer')->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('repricer')->__('Status'),
                        'values' => $statuses
                    )
                )
            )
        );

        Mage::dispatchEvent('repricer_adminhtml_competitors_grid_prepare_massaction', array('block' => $this));
        return $this;
    }



    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('competitor_id' => $row->getId()));
    }

}