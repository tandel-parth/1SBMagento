<?php

class Ccc_Filemanager_Model_Filemanager extends Varien_Data_Collection_Filesystem
{
    protected function _generateRow($filepath)
    {
        $row = parent::_generateRow($filepath);
        $row['file_name'] = basename($filepath);
        $row['folder_name'] = dirname($filepath);
        $row['created_date'] = $this->_getFormattedDate(filectime($filepath));
        $row['extension'] = pathinfo($filepath, PATHINFO_EXTENSION);
        return $row;
    }

    protected function _getFormattedDate($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
}
