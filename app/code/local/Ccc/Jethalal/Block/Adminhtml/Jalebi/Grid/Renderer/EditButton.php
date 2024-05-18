<?php

class Ccc_Jethalal_Block_Adminhtml_Jalebi_Grid_Renderer_EditButton extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $rowCounter = 0;
    public function render(Varien_Object $row)
    {
        // Render competitor information
        $jalebiId = $row->getData('jalebi_id');
        $status = array(
            '1' => 'Enabled',
            '2' => 'Disabled'
        );
        $status = json_encode($status);
        $editUrl = $this->getUrl('*/*/save', array('jalebi_id' => $jalebiId));
        $output = "<a href='#' class='edit-jalebi' data-url='{$editUrl}' data-jalebi-id='{$jalebiId}' data-status='{$status}'>Edit</a>";
        return $output;
    }
}
