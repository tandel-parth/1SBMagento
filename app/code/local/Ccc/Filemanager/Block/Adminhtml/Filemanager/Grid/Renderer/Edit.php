<?php

class Ccc_Filemanager_Block_Adminhtml_Filemanager_Grid_Renderer_Edit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $fileName = $row->getFileName();
        $folderPath = $row->getFolderName();
        $url = $this->getUrl('*/*/saveFileName');
        return "<div class='editable' data-file-name='{$fileName}' data-folder-path = '{$folderPath}' data-url='{$url}'>{$fileName}</div>";
    }
}
