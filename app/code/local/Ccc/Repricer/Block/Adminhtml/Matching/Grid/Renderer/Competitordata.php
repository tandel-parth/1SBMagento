<?php

class Ccc_Repricer_Block_Adminhtml_Matching_Grid_Renderer_Competitordata extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $rowCounter = 0;
    public function render(Varien_Object $row)
    {
        // Render competitor information
        $productId = $row->getData('product_id');
        $items = Mage::getModel('ccc_repricer/matching')->getCollection()->addFieldToFilter('product_id', $productId);
        $columnIndex = $this->getColumn()->getIndex();

        $items->getSelect()
            ->join(
                array('cpev' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
                'cpev.competitor_id = main_table.competitor_id',
                ['cpev.name AS competitor_name']
            );

        $this->getFilter($items);
        $reasons = Mage::getModel('ccc_repricer/matching')->getReasons();
        $output = "<table style='border: 0;'>";

        foreach ($items as $item) {
            $rowId = 'row_' . self::$rowCounter;
            self::$rowCounter++;

            $output .= "<tr id='$rowId' height='23vh'>";

            switch ($columnIndex) {
                case 'competitor_name':
                    $output .= "<td width='150px'>";
                    $output .= $item->getCompetitorName();
                    $output .= "</td>";
                    break;

                case 'competitor_url':
                    $itemId = $item->getId();
                    $output .= "<td class='editable-{$itemId} competitor-url' data-field='competitor_url' width='150px'>";
                    $output .= $item->getCompetitorUrl();
                    $output .= "</td>";
                    break;

                case 'competitor_sku':
                    $itemId = $item->getId();
                    $output .= "<td class='editable-{$itemId}  competitor-sku' data-field='competitor_sku' width='150px'>";
                    $output .= $item->getCompetitorSku();
                    $output .= "</td>";
                    break;

                case 'competitor_price':
                    $itemId = $item->getId();
                    $output .= "<td class='editable-{$itemId}  competitor-price' data-field='competitor_price' width='150px'>";
                    $output .= $item->getCompetitorPrice();
                    $output .= "</td>";
                    break;

                case 'reason':
                    $itemId = $item->getId();
                    $output .= "<td width='10px' class='editable-{$itemId}  reason' data-field='reason'>";
                    $output .= $reasons[$item->getReason()];
                    $output .= "</td>";
                    break;

                case 'updated_date':
                    $output .= "<td width='150px'>";
                    $output .= $item->getUpdatedDate();
                    $output .= "</td>";
                    break;

                case 'edit':
                    $formkey = Mage::getSingleton('core/session')->getFormKey();
                    $itemId = $item->getId();
                    $reasonArray = json_encode($reasons);
                    $editUrl = $this->getUrl('*/*/edit', array('repricer_id' => $itemId));
                    $output .= "<td width='50px' class='editable' data-field='edit_link'>";
                    $output .= "<a href='#' class='edit-row' data-url='{$editUrl}' data-repricer-id='{$itemId}' data-form-key='{$formkey}' data-reason='{$reasonArray}'>Edit</a>";
                    $output .= "</td>";
                    break;
            }
            $output .= "</tr>";
        }

        $output .= "</table>";
        return $output;
    }

    public function getFilter($items)
    {
        $request = $this->getColumn()->getGrid()->getRequest();
        $filterEncoded = $request->getParam('filter');
        $filterDecoded = base64_decode($filterEncoded);
        parse_str($filterDecoded, $filterArray);

        foreach ($filterArray as $key => $value) {
            switch ($key) {
                case 'competitor_name':
                    $items->getSelect()->where("main_table.competitor_id = ?", "{$value}");
                    break;
                case 'competitor_url':
                    $items->getSelect()->where("main_table.competitor_url LIKE ?", "%{$value}%");
                    break;
                case 'competitor_sku':
                    $items->getSelect()->where("main_table.competitor_sku LIKE ?", "%{$value}%");
                    break;
                case 'competitor_price':
                    $from = isset($value['from']) ? $value['from'] : 0;
                    $to = isset($value['to']) ? $value['to'] : null;
                    if (!empty($from)) {
                        $items->getSelect()->where("main_table.competitor_price >= ?", $from);
                    }
                    if (!empty($to)) {
                        $items->getSelect()->where("main_table.competitor_price <= ?", $to);
                    }
                    break;
                case 'updated_date':
                    if (isset($value['from'])) {
                        $value['from'] = date('Y-m-d 00:00:00', strtotime($value['from']));
                        $items->addFieldToFilter('main_table.updated_date', array('from' => $value['from'], 'datetime' => true));
                    }
                    if (isset($value['to'])) {
                        $value['to'] = date('Y-m-d 23:59:59', strtotime($value['to']));
                        $items->addFieldToFilter('main_table.updated_date', array('to' => $value['to'], 'datetime' => true));
                    }
                    break;
                case 'reason':
                    $items->getSelect()->where("main_table.reason LIKE ?", "{$value}");
                    break;
            }
        }
    }

}