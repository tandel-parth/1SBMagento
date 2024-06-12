<?php
class Ccc_Filemanager_Block_Adminhtml_Filemanager_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('fileGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $pathValue = $this->getData('parent_folder_path') ?: $this->getRequest()->getParam('parent_folder_path', null);
        $path = preg_replace('/\s+/', '', $pathValue);
        $targetDir = Mage::getBaseDir() . DS . $path;
        if (is_null($pathValue)) {
            $collection = Mage::getModel('ccc_filemanager/filemanager')
                ->addTargetDir($targetDir)
                ->setCollectRecursively(true)
                ->setDirsFilter('')
                ->setFilesFilter('');
            $collection->addFieldToFilter('file_name', array('eq' => ''));
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }

        if (is_dir($targetDir)) {
            $collection = Mage::getModel('ccc_filemanager/filemanager')
                ->addTargetDir($targetDir)
                ->setCollectRecursively(true)
                ->setDirsFilter('')
                ->setFilesFilter('');
            $this->setCollection($collection);
        } else {
            Mage::log("Invalid directory: " . $targetDir, null, 'filemanager.log');
        }
        return parent::_prepareCollection();
    }
    public function setParentFolderPath($path)
    {
        $this->setData('parent_folder_path', $path);
        return $this;
    }
    protected function _prepareColumns()
    {
        $columns = array(
            'created_date' => array(
                'header' => Mage::helper('filemanager')->__('File Created Date'),
                'align' => 'left',
                'width' => '200px',
                'type' => 'datetime',
                'index' => 'created_date',
                'filter' => false,
                'sortable' => true,
            ),
            'folder_name' => array(
                'header' => Mage::helper('filemanager')->__('Folder Path'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'folder_name',
                'type' => 'text',
                'sortable' => true,
            ),
            'file_name' => array(
                'header' => Mage::helper('filemanager')->__('File Name'),
                'align' => 'left',
                'index' => 'file_name',
                'type' => 'text',
                'renderer' => 'filemanager/adminhtml_filemanager_grid_renderer_edit',
                'sortable' => true,
            ),
            'extension' => array(
                'header' => Mage::helper('filemanager')->__('File Extension'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'extension',
                'type' => 'text',
                'sortable' => true,
            ),
            'action' => array(
                'header' => Mage::helper('filemanager')->__('Actions'),
                'align' => 'left',
                'width' => '200px',
                'type' => 'text',
                'index' => 'action',
                'filter' => false,
                'renderer' => 'filemanager/adminhtml_filemanager_grid_renderer_buttons',
                'sortable' => false,
            ),

        );

        foreach ($columns as $columnId => $columnData) {
            $this->addColumn($columnId, $columnData);
        }

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $parentFolderPath = $this->getRequest()->getParam('parent_folder_path');
        $sanitizedParentFolderPath = str_replace('\\', '_', $parentFolderPath);
        $encodedParentFolderPath = urlencode($sanitizedParentFolderPath);
        $url = $this->getUrl('*/*/grid', array('parent_folder_path' => $encodedParentFolderPath));
        return $url;
    }
}
