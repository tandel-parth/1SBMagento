<?php
class Ccc_Banner_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/show_all')) {
            $collection = Mage::getModel('ccc_banner/banner')->getCollection();
        } else {
            $collection = Mage::getModel('ccc_banner/banner')->getCollection()->setOrder('banner_id', 'DESC')->setPageSize(5);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/banner_id')) {
            $this->addColumn(
                'banner_id',
                array(
                    'header' => Mage::helper('banner')->__('Banner ID'),
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'banner_id',
                )
            );
        }
        if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/banner_name')) {
            $this->addColumn(
                'banner_name',
                array(
                    'header' => Mage::helper('banner')->__('Banner Name'),
                    'align' => 'left',
                    'index' => 'banner_name',
                )
            );
        }
        if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/banner_image')) {
            $this->addColumn(
                'banner_image',
                array(
                    'header' => Mage::helper('banner')->__('Banner Image'),
                    'align' => 'center',
                    'index' => 'banner_image',
                    'renderer' => 'Ccc_Banner_Block_Adminhtml_Banner_Grid_Renderer_Image', // Use custom renderer for image column
                )
            );
        }
        // $this->addColumn('banner_image', array(
        //     'header'    => Mage::helper('banner')->__('Banner Image'),
        //     'align'     =>'left',
        //     'index'     => 'banner_image',
        // ));
        if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/status')) {
            $this->addColumn(
                'status',
                array(
                    'header' => Mage::helper('banner')->__('Banner Status'),
                    'align' => 'left',
                    'index' => 'status',
                )
            );
        }
        if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/show_on')) {
            $this->addColumn(
                'show_on',
                array(
                    'header' => Mage::helper('banner')->__('Banner Show On'),
                    'align' => 'left',
                    'index' => 'show_on',
                )
            );
        }

        // Add more columns as needed

        return parent::_prepareColumns();
    }
    // MAss Actions 
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('banner_id'); // Change to 'banner_id'

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('banner')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('banner')->__('Are you sure you want to delete selected banners?')
            )
        );

        $statuses = Mage::getSingleton('ccc_banner/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('banner')->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('banner')->__('Status'),
                        'values' => $statuses
                    )
                )
            )
        );

        Mage::dispatchEvent('banner_adminhtml_banner_grid_prepare_massaction', array('block' => $this));
        return $this;
    }



    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('banner_id' => $row->getId()));
    }
}
