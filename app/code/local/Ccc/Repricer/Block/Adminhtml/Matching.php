<?php
class Ccc_Repricer_Block_Adminhtml_Matching extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_matching';
        $this->_blockGroup = 'repricer';
        $this->_headerText = Mage::helper('repricer')->__('Manage Matching');
        parent::__construct();
        $this->removeButton('add');
        $this->_addButton('upload_csv', array(
            'label'   => Mage::helper('repricer')->__('Upload CSV'),
            'onclick' => "document.getElementById('csv_file').click(); return true;", // Trigger file input click
            'class'   => 'upload',
        ), 0, 10, 'header');
    }
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        // Add file input element
        $html .= '<form id="upload_form" action="' . $this->getUrl('*/*/import') . '" method="post" enctype="multipart/form-data" style="display: none;">';
        $html .= '<input type="hidden" name="form_key" value="' . Mage::getSingleton('core/session')->getFormKey() . '">';
        $html .= '<input type="file" name="csv_file" id="csv_file" onchange="document.getElementById(\'upload_form\').submit();">';
        $html .= '</form>';

        return $html;
    }
}
