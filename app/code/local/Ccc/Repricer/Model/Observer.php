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
    public function uploadCsv()
    {
        $dataArray = Mage::getModel('ccc_repricer/matching')->getCollectionData()->getData();
        foreach ($dataArray as &$item) {
            unset($item['entity_type_id']);
            unset($item['attribute_id']);
            unset($item['competitor_id']);
            unset($item['repricer_id']);
            unset($item['reason']);
            unset($item['updated_date']);
        }

        $competitorData = array();
        $matchingCompetitorName = [];

        foreach ($dataArray as $data) {
            $competitorName = $data['competitor_name'];
            if (!in_array($competitorName, $matchingCompetitorName)) {
                $matchingCompetitorName[] = $competitorName;
            }

            if (!isset($competitorData[$competitorName])) {
                $competitorData[$competitorName] = array();
            }

            $competitorData[$competitorName][] = $data;
        }

        $filePaths = [];

        foreach ($matchingCompetitorName as $competitorName) {
            $dataArray = $competitorData[$competitorName];
            $csv = '';
            $headerRow = array_keys($dataArray[0]);
            $csv .= implode(',', $headerRow) . "\n";
            foreach ($dataArray as $row) {
                $csvRow = array();
                foreach ($row as $value) {
                    $value = str_replace('"', '""', $value);
                    $csvRow[] = '"' . $value . '"';
                }
                $csv .= implode(',', $csvRow) . "\n";
            }
            $filePath = Mage::getBaseDir('var') . DS . 'report' . DS . 'cmonitor' . DS . 'upload' . DS . $competitorName . '_upload_' . time() . '.csv';
            file_put_contents($filePath, $csv);
            $filePaths[] = $filePath;
        }
        return $filePaths;
    }
    public function downloadCsv()
    {
        $folderPath = Mage::getBaseDir('var') . DS . 'report' . DS . 'cmonitor' . DS . 'download';
        // Get the current timestamp
        $currentTime = time();
        // Scan the folder for CSV files added in the last 24 hours
        $files = glob($folderPath . DIRECTORY_SEPARATOR . '*_pending.csv');

        foreach ($files as $file) {
            $row = 0;
            $parsedData = [];
            $header = [];
            if (($handle = fopen($file, 'r')) !== FALSE) {
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
                    $rowData = array_combine($header, $data);

                    // Check if the record with the same competitor_name and competitor_sku exists in the database
                    $model = Mage::getModel('ccc_repricer/matching');
                    $existingRecord = $model->getCollectionData()
                        ->addFieldToFilter('name', $rowData['competitor_name'])
                        ->addFieldToFilter('competitor_sku', $rowData['competitor_sku'])->getData();
                    // Update the existing record
                    foreach ($existingRecord as $record) {
                        $data = array_merge($record, $rowData);
                        $model->setData($data);
                        $model->save();
                    }
                }
                fclose($handle);
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
}