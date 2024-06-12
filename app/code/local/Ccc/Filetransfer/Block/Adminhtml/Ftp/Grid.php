<?php
class Ccc_Filetransfer_Block_Adminhtml_Ftp_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_filetransfer/filetransfer')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'ftp_id' =>
            array(
                'header' => Mage::helper('filetransfer')->__('Ftp ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'ftp_id',
            ),

            'filepath' =>
            array(
                'header' => Mage::helper('filetransfer')->__('File Path'),
                'align' => 'left',
                'index' => 'filepath',
            ),

            'filename' =>
            array(
                'header' => Mage::helper('filetransfer')->__('File Name'),
                'align' => 'left',
                'index' => 'filename',
            ),

            'configuration_id' =>
            array(
                'header' => Mage::helper('filetransfer')->__('Configuration ID'),
                'align' => 'left',
                'index' => 'configuration_id',
            ),

            'created_at' =>
            array(
                'header' => Mage::helper('filetransfer')->__('Created Date'),
                'align' => 'left',
                'index' => 'created_at',
            ),
            'action' => array(
                'header' => Mage::helper('filetransfer')->__('Actions'),
                'align' => 'center',
                'width' => '200px',
                'type' => 'text',
                'index' => 'action',
                'filter' => false,
                'renderer' => 'filetransfer/adminhtml_ftp_grid_renderer_buttons',
                'sortable' => false,
            ),
        );


        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
}
