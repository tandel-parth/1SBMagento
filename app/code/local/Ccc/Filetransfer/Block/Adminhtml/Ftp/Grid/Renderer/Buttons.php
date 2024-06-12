<?php

class Ccc_Filetransfer_Block_Adminhtml_Ftp_Grid_Renderer_Buttons extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $id = $row->getFtpId();
        $fileName = $row->getFilename();
        $fileInfo  = pathinfo($fileName);
        $zipUrl = $this->getUrl('*/*/extractZip',array('id' => $id));
        $xmlToCsvUrl = $this->getUrl('*/*/convertToCsv',array('id' => $id));
        $button = "";
        if ($fileInfo['extension'] == 'zip') {
            $button .= sprintf('<a href="%s"><button type="button" onclick="this.preventDefault();">Extract Zip</button></a>', $zipUrl);
        } else if ($fileInfo['extension'] == 'xml') {
            $button .= sprintf('<a href="%s"><button type="button" onclick="this.preventDefault();">Convert To Csv</button></a>', $xmlToCsvUrl);
        } else {
            $button .= 'No Action';
        }
        return $button;
    }
}
