<?php
class Ccc_Repricer_Block_Adminhtml_Matching_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_repricer/matching')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        // Add columns for the grid
        $collumn = array(
            'repricer_id' =>
                array(
                    'header' => Mage::helper('repricer')->__('Repricer Id'),
                    'type' => 'number',
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'repricer_id',
                ),
            'product_id' =>
                array(
                    'header' => Mage::helper('repricer')->__('Product Id'),
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'product_id',
                ),
            'competitor_id' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Id'),
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'competitor_id',
                ),

            'name' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Name'),
                    'align' => 'left',
                    'index' => 'name',
                ),

            'competitor_url' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor URL'),
                    'align' => 'left',
                    'index' => 'competitor_url',
                ),



            'competitor_sku' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Product SKU'),
                    'align' => 'left',
                    'index' => 'competitor_sku',
                ),
            'competitor_price' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Product Price'),
                    'align' => 'left',
                    'index' => 'competitor_price',
                ),
            'status' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Status'),
                    'align' => 'left',
                    'index' => 'status',
                    'type' => 'options',
                    'options' => array(
                        '0'=> 'no match',
                        '1'=>'active',
                        '2'=>'out of stock',
                        '3'=>'not available',
                        '4'=>'rong match'
                    ),
                ),
            'updated_date' =>
                array(
                    'header' => Mage::helper('repricer')->__('Competitor Updated Date'),
                    'align' => 'left',
                    'type' => 'datetime',
                    'index' => 'updated_date',
                    'renderer' => 'repricer/adminhtml_matching_grid_renderer_datetime',
                )
        );

        foreach ($collumn as $collumnName => $collumnKey) {
            $this->addColumn($collumnName, $collumnKey);
        }

        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('repricer_id' => $row->getId()));
    }
}