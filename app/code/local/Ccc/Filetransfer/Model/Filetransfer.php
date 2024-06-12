<?php
class Ccc_Filetransfer_Model_Filetransfer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_filetransfer/filetransfer');
    }
    public function saveExtractedFilesData($extractPath, $configId)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractPath));
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $data = array(
                    'filename' => $file->getFilename(),
                    'filepath' => $file->getPath(),
                    'configuration_id' => $configId
                );
                $this->setData($data)->save();
            }
        }
    }
}

?>

