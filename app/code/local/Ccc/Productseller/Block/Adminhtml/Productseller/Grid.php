<?php
class Ccc_Productseller_Block_Adminhtml_Productseller_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Ccc_Productseller/productseller')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'id' =>
            array(
                'header' => Mage::helper('productseller')->__('Seller Id'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'id',
            ),

            'seller_name' =>
            array(
                'header' => Mage::helper('productseller')->__('Seller Name'),
                'align' => 'left',
                'index' => 'seller_name',
            ),

            'company_name' =>
            array(
                'header' => Mage::helper('productseller')->__('Company Name'),
                'align' => 'left',
                'index' => 'company_name',
            ),

            'address' =>
            array(
                'header' => Mage::helper('productseller')->__('Address'),
                'align' => 'left',
                'index' => 'address',
            ),

            'city' =>
            array(
                'header' => Mage::helper('productseller')->__('City'),
                'align' => 'left',
                'index' => 'city',
            ),

            'state' =>
            array(
                'header' => Mage::helper('productseller')->__('State'),
                'align' => 'left',
                'index' => 'state',
            ),

            'county' =>
            array(
                'header' => Mage::helper('productseller')->__('Country'),
                'align' => 'left',
                'index' => 'county',
            ),

            'is_active' =>
            array(
                'header' => Mage::helper('productseller')->__('Active'),
                'align' => 'left',
                'index' => 'is_active',
                'type' => 'options',
                'options' =>  array(
                    1 => Mage::helper('productseller')->__('YES'),
                    2 => Mage::helper('productseller')->__('NO')
                ),
            ),

            'created_at' =>
            array(
                'header' => Mage::helper('productseller')->__('Created Date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'created_at',

            ),
            'updated_date' =>
            array(
                'header' => Mage::helper('productseller')->__('Updated Date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'updated_date',
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
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('productseller')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('productseller')->__('Are you sure you want to delete selected competitors?')
            )
        );

        $statuses = array(
            1 => Mage::helper('productseller')->__('YES'),
            2 => Mage::helper('productseller')->__('NO')
        );

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'is_active',
            array(
                'label' => Mage::helper('productseller')->__('Change Active'),
                'url' => $this->getUrl('*/*/massActive', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'is_active',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('productseller')->__('Active'),
                        'values' => $statuses
                    )
                )
            )
        );

        Mage::dispatchEvent('productseller_adminhtml_productseller_grid_prepare_massaction', array('block' => $this));
        return $this;
    }



    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
