<?php

class Ccc_Repricer_Model_Observer
{

    const TIME_SPAN = 24 * 3600;
    public function setCompetitorData()
    {
        $timestamp = time();
        $sqlTimestamp = date('Y-m-d H:i:s', $timestamp);
        $pdata = [];
        $data = (Mage::getModel('ccc_repricer/competitors')->getCollection());
        foreach ($data as $_data) {
            $difference = strtotime($sqlTimestamp) - strtotime($_data->getCreatedAt());
            if ($difference <= Ccc_Repricer_Model_Observer::TIME_SPAN) {
                $productdata = Mage::getModel('catalog/product')->getCollection();
                $productdata->addAttributeToSelect(['name', 'status']);
                $productdata->addAttributeToFilter('status', 1);
                echo "<pre>";
                foreach ($productdata as $_product) {
                    // Initialize $pdata for each product
                    $pdata[] = [
                        'competitor_id' => $_data->getCompetitorId(),
                        'product_id' => $_product->getId(),
                    ];
                }
            }
        }
        if (!empty($pdata)) {
            $matchingModel = Mage::getModel('ccc_repricer/matching');
            foreach ($pdata as $data) {
                $matchingModel->setData($data);
                $matchingModel->save();
            }
        } else {
            echo "No data to save.";
        }
    }

    public function setProductData()
    {
        $timestamp = time();
        $sqlTimestamp = date('Y-m-d H:i:s', $timestamp);
        $pdata = [];
        $data = (Mage::getModel('catalog/product')->getCollection());
        $data->addAttributeToSelect(['name', 'status']);
        $data->addAttributeToFilter('status', 1);
        foreach ($data as $_product) {
            $difference = strtotime($sqlTimestamp) - strtotime($_product->getCreatedAt());
            if ($difference <= Ccc_Repricer_Model_Observer::TIME_SPAN) {
                $cdata = Mage::getModel('ccc_repricer/competitors')->getCollection();
                foreach ($cdata as $_data) {
                    $timediff = strtotime($sqlTimestamp) - strtotime($_data->getCreatedAt());
                    if ($timediff >= Ccc_Repricer_Model_Observer::TIME_SPAN) {
                        $pdata[] = [
                            'competitor_id' => $_data->getCompetitorId(),
                            'product_id' => $_product->getId(),
                        ];
                    }
                }
            }
        }
        if (!empty($pdata)) {
            $matchingModel = Mage::getModel('rccc_epricer/matching');
            foreach ($pdata as $data) {
                $matchingModel->setData($data);
                $matchingModel->save();
            }
        } else {
            echo "No data to save.";
        }
    }
    public function downloadCsv()
    {
        $folderPath = Mage::getBaseDir('var') . DS . 'report' . DS . 'cmonitor' . DS . 'download';
        // Get the current timestamp
        $currentTime = time();
        // Scan the folder for CSV files added in the last 24 hours
        $files = glob($folderPath . DIRECTORY_SEPARATOR . '*_pending.csv');

        foreach ($files as $file) {
            $fileModificationTime = filemtime($file);
            $parsedData = $this->_parseCsv($file);
            foreach ($parsedData as $row) {
                
                $model = Mage::getModel('ccc_repricer/matching')->setData($row)->save();
            }
            if ($model) {
                $oldName = $file;
                $newName = str_replace("_pending", "_completed_" . time(), $oldName);
                if (file_exists($oldName)) {
                    if (copy($oldName, $newName)) {
                        // If copy is successful, delete the original file
                        if (unlink($oldName)) {
                            echo "File renamed successfully.";
                        } else {
                            echo "Error deleting original file.";
                        }
                    } else {
                        echo "Error copying file.";
                    }
                } else {
                    echo "File not found: " . $oldName;
                }
            }
        }
    }
    protected function _parseCsv($csvFile)
    {
        $row = 0;
        $parsedData = [];
        $header = [];

        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$row) {
                    // First row contains headers
                    foreach ($data as &$item) {
                        $item = str_replace(' ', '_', strtolower($item));
                    }
                    $header = $data;
                    $row++;
                    continue;
                }

                // Combine headers with data for current row
                $parsedData[] = array_combine($header, $data);
            }
            fclose($handle);
        }
        return $parsedData;
    }

}