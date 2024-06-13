<?php

class Ccc_Filetransfer_Adminhtml_FtpController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('ccc_filetransfer/filetransfer')
            ->_addBreadcrumb(Mage::helper('filetransfer')->__('Manage Ftp'), Mage::helper('filetransfer')->__('Manage Ftp'));
        return $this;
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Manage Ftp"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function extractZipAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ccc_filetransfer/filetransfer')->load($id);
        $configId = $model->getConfigurationId();
        $filePath = $model->getFilepath() . DS . $model->getFilename();
        $fileInfo  = pathinfo($model->getFilename());
        $extractTo = $model->getFilepath() . DS . $fileInfo['filename'];

        $zip = new ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
            $model = Mage::getModel('ccc_filetransfer/filetransfer');
            $model->saveExtractedFilesData($extractTo, $configId);
            Mage::getSingleton('adminhtml/session')->addSuccess("Extraction complete!");
        } else {
            Mage::getSingleton('adminhtml/session')->addError("Failed to open zip file");
        }
        $this->_redirect('*/*/');
    }
    public function convertToCsvAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ccc_filetransfer/filetransfer')->load($id);
        $xmlFile = $model->getFilepath() . DS . $model->getFilename();

        libxml_use_internal_errors(true); // Enable user error handling for libxml

        $xml = simplexml_load_file($xmlFile);

        if ($xml === false) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                Mage::getSingleton('adminhtml/session')->addError("XML Error: " . $error->message);
            }
            libxml_clear_errors();
            $this->_redirect('*/*/');
            return;
        }
        $headers = array();
        $dataRows = array();

        foreach ($xml->items->children() as $item) {
            $row = array();
            $this->buildHeadersAndData($item, 'items_item', $headers, $row);
            $dataRows[] = $row;
        }

        $headers = array_unique($headers);

        $csvFile = $model->getFilepath() . DS . pathinfo($model->getFilename(), PATHINFO_FILENAME) . '.csv';
        $this->generateCsv($csvFile, $headers, $dataRows);
        Mage::getSingleton('adminhtml/session')->addSuccess("Conversion from XML to CSV complete!");
        $this->_redirect('*/*/');
    }
    protected function buildHeadersAndData($xml, $prefix, &$headers, &$row)
    {
        foreach ($xml->children() as $child) {
            $tagName = $child->getName();
            $header = $prefix . '_' . $tagName;

            if (!in_array($header, $headers)) {
                $headers[] = $header;
            }

            if ($child->count() > 0) {
                $this->buildHeadersAndData($child, $header, $headers, $row);
            } else {
                $row[$header] = (string)$child['value'];
            }
        }
    }

    public function csvAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ccc_filetransfer/filetransfer')->load($id);
        $xmlFile = $model->getFilepath() . DS . $model->getFilename();

        libxml_use_internal_errors(true); // Enable user error handling for libxml

        $xml = simplexml_load_file($xmlFile);

        if ($xml === false) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                Mage::getSingleton('adminhtml/session')->addError("XML Error: " . $error->message);
            }
            libxml_clear_errors();
            $this->_redirect('*/*/');
            return;
        }

        // Define the mapping array
        $data = Mage::helper('filetransfer')->csvData();

        $headers = array_keys($data);
        $dataRows = array();

        foreach ($xml->items->item as $item) {
            $row = array();
            foreach ($data as $header => $path) {
                $row[$header] = $this->getValueFromPath($item, $path);
            }
            $dataRows[] = $row;
        }
        $csvFile = $model->getFilepath() . DS . pathinfo($model->getFilename(), PATHINFO_FILENAME) . '.csv';
        $this->generateCsv($csvFile, $headers, $dataRows);

        Mage::getSingleton('adminhtml/session')->addSuccess("Conversion from XML to CSV complete!");
        $this->_redirect('*/*/');
    }
    protected function getValueFromPath($xml, $path)
    {
        $values = [];
        $segments = explode(':', $path);
        $attribute = null;
        $segment = $segments[0];
        $attribute = $segments[1];
        $segments = explode('.', $segment);

        // Recursive function to traverse and collect values
        $this->collectValues($xml, $segments, $attribute, $values);

        return implode(', ', $values); // Concatenate multiple values as a single string
    }
    protected function collectValues($xml, $segments, $attribute, &$values)
    {
        $segment = array_shift($segments);

        // Traverse XML according to path
        if ($segment && isset($xml->$segment)) {
            if (empty($segments)) {
                // If no more segments, collect the values
                foreach ($xml->$segment as $item) {
                    $values[] = $attribute ? (string)$item[$attribute] : (string)$item;
                }
            } else {
                // Continue traversing
                foreach ($xml->$segment as $item) {
                    $this->collectValues($item, $segments, $attribute, $values);
                }
            }
        }
    }
    protected function generateCsv($filePath, $headers, $dataRows)
    {
        $fp = fopen($filePath, 'w');
        fputcsv($fp, $headers);
        foreach ($dataRows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}
