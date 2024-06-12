<?php

class Ccc_Filemanager_Block_Adminhtml_Filemanager_Grid_Renderer_Buttons extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $fileName = $row->getFileName();
        $folderPath = $row->getFolderName();
        $filePath = $folderPath . '\\' . $fileName;
        $deleteUrl = $this->getUrl('*/*/delete');
        $downloadUrl = $this->getUrl('*/*/download',array('file_path' => base64_encode($filePath), 'file_name' => base64_encode($fileName)));
        return "<button type='button' id='delete_file' data-file-path='{$filePath}' data-url='{$deleteUrl}'>Delete</button> ".sprintf(' &nbsp;<a href="%s"><button type="button" onclick="this.preventDefault();">Download</button></a>',$downloadUrl);
    }
}
