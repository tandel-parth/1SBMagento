<?php

class Ccc_Filemanager_Adminhtml_FilemanagerController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('ccc_filemanager/filemanager')
            ->_addBreadcrumb(Mage::helper('filemanager')->__('Manage Filemanager'), Mage::helper('filemanager')->__('Manage Filemanager'));
        return $this;
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Manage Banners"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function gridAction()
    {
        $path = $this->getRequest()->getParam('parent_folder_path');
        $pathMain = str_replace('_', '\\', $path);
        $this->loadLayout(false);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('filemanager/adminhtml_filemanager_grid')->setParentFolderPath($pathMain)->toHtml()
        );
    }
    public function saveFileNameAction()
{
    $response = array();
    if ($this->getRequest()->isXmlHttpRequest()) {
        try {
            // Your processing logic here
            $fileName = $this->getRequest()->getPost('fileName');
            $folderPath = $this->getRequest()->getPost('folderPath');
            $newValue = $this->getRequest()->getPost('newValue');

            $oldFilePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;
            $newFilePath = $folderPath . DIRECTORY_SEPARATOR . $newValue;
            
            // Check if the new file name already exists in the folder
            if (file_exists($newFilePath)) {
                throw new Exception('A file with the new name already exists in the folder.');
            }

            // Rename the file
            if (!rename($oldFilePath, $newFilePath)) {
                throw new Exception('File renaming failed.');
            }

            $response['success'] = true;
            $response['message'] = 'File name saved successfully';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid request';
    }

    $this->getResponse()->setHeader('Content-type', 'application/json');
    $this->getResponse()->setBody(json_encode($response));
}


    public function deleteAction()
    {
        $success = false;
        if ($this->getRequest()->isXmlHttpRequest()) {
            $deletedFile = $this->getRequest()->getPost('deletedFile');

            if (file_exists($deletedFile)) {
                if (unlink($deletedFile)) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($success);
    }
    public function downloadAction()
    {
        $file_path = base64_decode($this->getRequest()->getParam('file_path'));
        $file_name = base64_decode($this->getRequest()->getParam('file_name'));
        if (file_exists($file_path)) {
            var_dump($file_path);
            $this->_prepareDownloadResponse($file_name, array('type' => 'filename', 'value' => $file_path));
        } else {
            echo "File not found.";
        }
    }
}
