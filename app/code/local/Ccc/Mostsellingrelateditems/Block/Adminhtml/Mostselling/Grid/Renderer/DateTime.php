<?php 

class Ccc_mostsellingrelateditems_Block_Adminhtml_Mostselling_Grid_Renderer_DateTime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $date = $this->_getValue($row);
        if ($date != '') {
            return $date;
        }
        return '';
    }
}