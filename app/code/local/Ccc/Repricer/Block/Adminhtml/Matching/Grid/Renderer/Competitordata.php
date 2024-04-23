<?php

class Ccc_Repricer_Block_Adminhtml_Matching_Grid_Renderer_Competitordata extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        // Render competitor information
        $productId = $row->getData('product_id');
        $reason = $row->getData('reason');
        $items = Mage::getModel('ccc_repricer/matching')->getCollection()->addFieldToFilter('product_id', $productId);
        $items->getSelect()
            ->join(
                array('cpev' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
                'cpev.competitor_id = main_table.competitor_id',
                ['cpev.name AS competitor_name']
            );
        $reason = Mage::getModel('ccc_repricer/matching')->getReasons();
        $output = "<table style='border: 0;'>";
        $columnIndex = $this->getColumn()->getIndex();
        switch ($columnIndex) {
            case 'competitor_name':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23vh>";
                    $output .= "<td width = 150px>";
                    $output .= $item->getCompetitorName();
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;
            case 'competitor_url':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23vh>";
                    $output .= "<td>";
                    $output .= $item->getCompetitorUrl();
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;

            case 'competitor_sku':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23>";
                    $output .= "<td>";
                    $output .= $item->getCompetitorSku();
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;

            case 'competitor_price':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23vh>";
                    $output .= "<td>";
                    $output .= $item->getCompetitorPrice();
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;

            case 'reason':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23vh>";
                    $output .= "<td width = 10px>";
                    $output .= $reason[$item->getReason()];
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;

            case 'updated_date':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23vh>";
                    $output .= "<td width = 150px>";
                    $output .= $item->getUpdatedDate();
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;
            case 'edit':
                $output = "<table style='border: 0;'>";
                foreach ($items as $item) {
                    $output .= "<tr height=23vh>";
                    $output .= "<td width = 50px>";
                    $output .= "<a href='" . $this->getUrl('*/*/edit', array('repricer_id' => $item->getId())) . "'>Edit</a>";
                    $output .= "</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
                return $output;
        }
    }
}