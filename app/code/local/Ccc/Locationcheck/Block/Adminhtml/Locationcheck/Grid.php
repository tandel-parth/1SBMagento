<?php
class Ccc_Locationcheck_Block_Adminhtml_Locationcheck_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Ccc_Locationcheck/locationcheck')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'id' =>
            array(
                'header' => Mage::helper('locationcheck')->__('Location Id'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'id',
            ),

            'zipcode' =>
            array(
                'header' => Mage::helper('locationcheck')->__('Location zipcode'),
                'align' => 'left',
                'index' => 'zipcode',
            ),

            'is_active' =>
            array(
                'header' => Mage::helper('locationcheck')->__('Active'),
                'align' => 'left',
                'index' => 'is_active',
                'type' => 'options',
                'options' =>  array(
                    1 => Mage::helper('locationcheck')->__('YES'),
                    2 => Mage::helper('locationcheck')->__('NO')
                ),
            ),

            'created_at' =>
            array(
                'header' => Mage::helper('locationcheck')->__('Created Date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'created_at',

            ),
            'updated_at' =>
            array(
                'header' => Mage::helper('locationcheck')->__('Updated Date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'updated_at',
            )
        );

        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
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

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('locationcheck')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('locationcheck')->__('Are you sure you want to delete selected Location?')
            )
        );

        $statuses = array(
            1 => Mage::helper('locationcheck')->__('YES'),
            2 => Mage::helper('locationcheck')->__('NO')
        );

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'is_active',
            array(
                'label' => Mage::helper('locationcheck')->__('Change Active'),
                'url' => $this->getUrl('*/*/massActive', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'is_active',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('locationcheck')->__('Active'),
                        'values' => $statuses
                    )
                )
            )
        );

        Mage::dispatchEvent('locationcheck_adminhtml_locationcheck_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
}