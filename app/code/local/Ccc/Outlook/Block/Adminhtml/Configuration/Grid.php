<?php
class Ccc_Outlook_Block_Adminhtml_Configuration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('configurationGrid');
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_outlook/configuration')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'configration_id' =>
            array(
                'header' => Mage::helper('outlook')->__('Configuration Id'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'configration_id',
            ),

            'username' =>
            array(
                'header' => Mage::helper('outlook')->__('Username'),
                'align' => 'left',
                'index' => 'username',
                'type' => 'text',
            ),

            'email' =>
            array(
                'header' => Mage::helper('outlook')->__('User E-mail'),
                'align' => 'left',
                'index' => 'email',
                'type' => 'text',
            ),

            'api_key' =>
            array(
                'header' => Mage::helper('outlook')->__('Api Key'),
                'align' => 'left',
                'width' => '50px',
                'type' => 'text',
                'index' => 'api_key',

            ),
            'token' =>
            array(
                'header' => Mage::helper('outlook')->__('Api Token'),
                'align' => 'left',
                'width' => '50px',
                'type' => 'text',
                'index' => 'token',
            )
        );
        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('configration_id');
        $this->getMassactionBlock()->setFormFieldName('configration_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('outlook')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('outlook')->__('Are you sure you want to delete selected configuration?')
            )
        );

        Mage::dispatchEvent('outlook_adminhtml_configuration_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('configration_id' => $row->getId()));
    }
}
