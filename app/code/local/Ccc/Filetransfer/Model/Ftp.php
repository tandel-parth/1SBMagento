<?php
class Ccc_Filetransfer_Model_Ftp extends Varien_Io_Ftp
{
    protected $_config;

    public function setConfigModel($collection)
    {
        $this->_config = $collection;
        return $this;
    }

    public function downloadFile()
    {
        if ($this->_config === null) {
            throw new Exception("Config model is not set.");
        }

        $configurationId = $this->_config->getId();
        $this->open(array(
            'host'     => $this->_config->getHost(),
            'user'     => $this->_config->getUsername(),
            'password' => $this->_config->getPassword(),
            'port'     => $this->_config->getPort()
        ));

        $localDir = Mage::helper('filetransfer')->localDir();
        if (!file_exists($localDir)) {
            mkdir($localDir, 0755, true);
        }

        $this->downloadDirectory('.', $localDir, $configurationId);

        // Close the FTP connection
        $this->close();
    }

    private function downloadDirectory($remoteDir, $localDir, $configurationId)
    {
        if (!file_exists($localDir)) {
            mkdir($localDir, 0755, true);
        }

        $fileList = ftp_nlist($this->_conn, $remoteDir);
        if ($fileList === false) {
            Mage::log("Failed to list directory: " . $remoteDir, null, 'ftp_errors.log');
            return;
        }

        foreach ($fileList as $file) {
            $remoteFile = $file;
            $localFile = $localDir . DS . $file;
            if (strpos($remoteFile, 'temp') === false) {
                if ($this->isDirectory($remoteFile)) {
                    $completedDirPath = 'temp/' . $remoteFile;
                    echo $completedDirPath;
                    echo "\n";

                    if (!($this->ftpDirectoryExists($this->_conn, $completedDirPath))) {
                        ftp_mkdir($this->_conn, $completedDirPath);
                    }
                    // Append the basename of the remote directory to the local directory
                    $this->downloadDirectory($remoteFile, $localDir . DS . basename($remoteFile), $configurationId);
                } else {
                    // Download the file
                    if (ftp_mdtm($this->_conn, $remoteFile) == -1) {
                        Mage::log("Could not retrieve modification time for file: " . $remoteFile, null, 'ftp_errors.log');
                        continue;
                    }

                    $fileInfo = pathinfo($remoteFile);
                    $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
                    $fileName = $fileInfo['filename'] . '_' . date('Ymd_His', ftp_mdtm($this->_conn, $remoteFile)) . $extension;
                    $localFilePath = $localDir . DS . $fileName;

                    if ($this->read($remoteFile, $localFilePath)) {
                        $data = array(
                            'filepath' => $localDir,
                            'filename' => $fileName,
                            'configuration_id' => $configurationId,
                        );
                        $fptData = Mage::getModel('ccc_filetransfer/filetransfer');
                        $fptData->setData($data)->save();

                        $filenewpath = 'temp/' . $remoteFile;
                        $fol = $this->mv($remoteFile, $filenewpath);
                    }
                }
            }
        }
    }


    private function isDirectory($remoteFile)
    {
        $originalDirectory = ftp_pwd($this->_conn);
        if (@ftp_chdir($this->_conn, $remoteFile)) {
            ftp_chdir($this->_conn, $originalDirectory);
            return true;
        }
        return false;
    }
    private function ftpDirectoryExists($ftp_conn, $dir)
    {
        $list = ftp_nlist($ftp_conn, $dir);
        if (is_array($list)) {
            return true;
        }
        return false;
    }
}
