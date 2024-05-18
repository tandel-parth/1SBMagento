<?php

class Ccc_Repricer_Model_Observer
{
    public function setNewData()
    {
        $tmp = [];
        $competitorIds = Mage::getModel('ccc_repricer/competitors')
            ->getCollection()
            ->getAllIds();

        $data = [];
        foreach ($competitorIds as $_competitorId) {
            $collection = Mage::getModel('catalog/product')
                ->getCollection();
            $collection->getSelect()
                ->columns('e.entity_id')
                ->joinLeft(
                    ['CRM' => 'ccc_repricer_matching'],
                    "e.entity_id = CRM.product_id AND CRM.competitor_id = {$_competitorId}",
                    ['competitor_id']
                )
                ->where('CRM.competitor_id IS NULL');
            $columns = [
                'product_id' => 'e.entity_id',
            ];
            $collection->getSelect()->order('product_id ASC')->reset(Zend_Db_Select::COLUMNS)
                ->columns($columns);
            foreach ($collection->getData() as $_data) {
                $_data['competitor_id'] = $_competitorId;
                $data[] = $_data;
            }
            $tmp[$_competitorId] = $collection->getColumnValues('product_id');
            if (!is_null($tmp[$_competitorId]) && !empty($tmp[$_competitorId])) {
                print_r($tmp[$_competitorId]);
            }
        }
        if (!is_null($data) && !empty($data)) {
            $model = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tableName = Mage::getSingleton('core/resource')->getTableName('ccc_repricer/matching');
            $result = $model->insertMultiple($tableName, $data);
        }
        if ($result) {
            echo "$result Rows Inserted successfully.";
        } else {
            echo "There is no new Product Id and Competitor Id...";
        }
    }

    public function uploadCsv()
    {
        $collection = Mage::getModel('ccc_repricer/matching')->getCollectionData();
        $columns = [
            'product_id' => 'product_id',
            'product_sku' => 'pro.sku',
            'competitor_name' => 'cpev.name',
            'competitor_url' => 'competitor_url',
            'competitor_sku' => 'competitor_sku',
        ];
        $collection->getSelect()->order('repricer_id ASC')->reset(Zend_Db_Select::COLUMNS)
            ->columns($columns);
        $dataArray = $collection->getData();

        $competitorData = array();
        $matchingCompetitorName = [];

        foreach ($dataArray as $data) {
            $competitorName = $data['competitor_name'];
            if (!in_array($competitorName, $matchingCompetitorName)) {
                $matchingCompetitorName[] = $competitorName;
            }
            $competitorData[$competitorName][] = $data;
        }

        $filePaths = [];

        foreach ($matchingCompetitorName as $competitorName) {
            $dataArray = $competitorData[$competitorName];
            $csv = '';
            $headerRow = array_keys($dataArray[0]);
            $csv .= implode(',', $headerRow) . "\n";
            foreach ($dataArray as $index => $row) {
                $csvRow = array();
                foreach ($row as $value) {
                    $value = str_replace('"', '""', $value);
                    $csvRow[] = '"' . $value . '"';
                }
                $csv .= implode(',', $csvRow);
                if ($index < count($dataArray) - 1) { // Check if not the last row
                    $csv .= "\n"; // Add newline character if not the last row
                }
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
        // Scan the folder for CSV files added in the last 24 hours
        $files = glob($folderPath . DIRECTORY_SEPARATOR . '*_pending.csv');

        foreach ($files as $file) {
            $row = 0;
            $header = [];
            if (($handle = fopen($file, 'r')) !== FALSE) {
                $mainData = [];
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
                    $mainData[] = $rowData;
                }
                fclose($handle);
            }
            $model = Mage::getModel('ccc_repricer/matching');
            $competitorId = Mage::getModel('ccc_repricer/competitors')
                ->load($rowData['competitor_name'], 'name')->getId();
            $collection = $model->getCollection()
                ->addFieldToFilter('competitor_sku', array('in' => array_column($mainData, 'competitor_sku')))
                ->addFieldToFilter('competitor_id', $competitorId);

            // Create an associative array to map competitor_sku to repricer_id
            $skuToRepricerIdMap = array();
            foreach ($collection as $item) {
                $skuToRepricerIdMap[$item->getCompetitorSku()] = $item->getRepricerId();
            }
            foreach ($mainData as $saveData) {
                if (isset($skuToRepricerIdMap[$saveData['competitor_sku']])) {
                    $saveData['repricer_id'] = $skuToRepricerIdMap[$saveData['competitor_sku']];
                } else {
                    Mage::log("No repricer_id found for competitor_sku: " . $saveData['competitor_sku']);
                }

                $csvData[] = $saveData;
            }
            $model = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tableName = Mage::getSingleton('core/resource')->getTableName('ccc_repricer/matching');

            $insertData = [];
            foreach ($csvData as $data) {
                unset($data['product_sku']);
                unset($data['competitor_name']);
                $insertData[] = $data;
            }

            $result = $model->insertOnDuplicate($tableName, $insertData);
            if ($result) {
                $oldName = $file;
                $newName = str_replace("_pending", "_completed_" . time(), $oldName);
                if (file_exists($oldName)) {
                    if (rename($oldName, $newName)) {
                        echo "File renamed successfully.";
                    } else {
                        echo "Error rename file.";
                    }
                } else {
                    echo "File not found: " . $oldName;
                }
            }
        }
    }
    public function hideMassactionClass()
    {
        echo '<script src="lib/jquery/jquery-1.10.2.js" type="text/html"> var j = jQuery.noConflict(); jQuery(document).ready(function($) {j(".headings th:first-child, .filter th:first-child, .a-center").hide();
        j("[name=\'massaction\']").hide(); // Use backslashes to escape inner quotes
        j(".massaction-checkbox").hide();
        j(".massaction").hide();
        j(".pc_combine").hide();
        document.querySelector(".pc-col").style.width = "0px"});</script>';
        // echo "<script>console.log(255);
        // </script>";
    }
}
