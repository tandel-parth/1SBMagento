<?php
class Ccc_Filetransfer_Block_Adminhtml_Configuration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_filetransfer/configuration')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'configuration_id' =>
            array(
                'header' => Mage::helper('filetransfer')->__('Configuration ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'configuration_id',
            ),

            'username' =>
            array(
                'header' => Mage::helper('filetransfer')->__('User Name'),
                'align' => 'left',
                'index' => 'username',
            ),

            'password' =>
            array(
                'header' => Mage::helper('filetransfer')->__('User Password'),
                'align' => 'center',
                'index' => 'password',
            ),

            'host' =>
            array(
                'header' => Mage::helper('filetransfer')->__('Host'),
                'align' => 'left',
                'index' => 'host',
            ),

            'port' =>
            array(
                'header' => Mage::helper('filetransfer')->__('Post'),
                'align' => 'left',
                'index' => 'port',
            )
        );


        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('configuration_id');
        $this->getMassactionBlock()->setFormFieldName('configuration_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('filetransfer')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('outlook')->__('Are you sure you want to delete selected configuration?')
            )
        );
        return $this;
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('configuration_id' => $row->getId()));
    }
}
