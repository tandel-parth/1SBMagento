<?php
class Ccc_Banner_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    // protected function _prepareCollection()
    // {
    //     $collection = Mage::getModel('ccc_banner/banner')->getCollection()->setOrder('banner_id');

    //     $this->setCollection($collection);
    //     return parent::_prepareCollection();
    // }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_banner/banner')->getCollection();
        if (!Mage::getSingleton('admin/session')->isAllowed('ccc_banner/show_all')) {
            $collection->setOrder('banner_id', 'DESC');
            $collection->getSelect()->limit(5);
        }
        $this->setCollection($collection);
        $this->getCollection()->load();
        return parent::_prepareCollection();
    }
    // protected function _preparePage()
    // {
    //     if (Mage::getSingleton('admin/session')->isAllowed('ccc_banner/show_all')) {
    //         $this->getCollection()->setPageSize((int) $this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
    //         $this->getCollection()->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
    //     } else {
    //         $this->getCollection()->setPageSize(5)->setOrder('banner_id', 'DESC');
    //         $this->getCollection()->setCurPage(1);
    //     }
    // }

    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'banner_id' =>
                array(
                    'header' => Mage::helper('banner')->__('Banner ID'),
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'banner_id',
                    'is_allowed' => Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/banner_id'),
                ),

            'banner_name' =>
                array(
                    'header' => Mage::helper('banner')->__('Banner Name'),
                    'align' => 'left',
                    'index' => 'banner_name',
                    'is_allowed' => Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/banner_name'),
                ),

            'banner_image' =>
                array(
                    'header' => Mage::helper('banner')->__('Banner Image'),
                    'align' => 'center',
                    'index' => 'banner_image',
                    'is_allowed' => Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/banner_image'),
                    'renderer' => 'Ccc_Banner_Block_Adminhtml_Banner_Grid_Renderer_Image', // Use custom renderer for image column
                ),

            'status' =>
                array(
                    'header' => Mage::helper('banner')->__('Banner Status'),
                    'align' => 'left',
                    'index' => 'status',
                    'is_allowed' => Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/status'),
                ),

            'show_on' =>
                array(
                    'header' => Mage::helper('banner')->__('Banner Show On'),
                    'align' => 'left',
                    'index' => 'show_on',
                    'is_allowed' => Mage::getSingleton('admin/session')->isAllowed('ccc_banner/collumn/show_on'),
                )
        );


        foreach ($collumn as $collumnName => $collumnKey) {
            if ($collumnKey['is_allowed'] == true) {
                $this->addColumn($collumnName, $collumnKey);
            }
        }

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
