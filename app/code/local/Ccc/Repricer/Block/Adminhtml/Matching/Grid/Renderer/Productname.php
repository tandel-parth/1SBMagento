<?php

class Ccc_Repricer_Block_Adminhtml_Matching_Grid_Renderer_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    // Use a static variable to track rendered product IDs
    protected static $renderedProductIds = [];

    public function render(Varien_Object $row)
    {
        $productId = $row->getData('product_id'); 
        $productName = $row->getData('product_name');
        $productSku = $row->getData('product_sku');

        if (in_array($productId, self::$renderedProductIds)) {
            return '';
        }

        self::$renderedProductIds[] = $productId;

        $displayText = $productName;
        if (!empty($productSku)) {
            $displayText .= ' (' . $productSku . ')';
        }
        return $displayText;
    }
}

?>
