<?php
class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling_Relatedgrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('relatedProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');

        $filterCollection = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems')
            ->getCollection()
            ->addFieldToFilter('most_selling_product_id', $id);

        $relatedProductIds = [];
        foreach ($filterCollection as $filter) {
            $relatedProductIds[] = $filter->getRelatedProductId();
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', ['in' => $relatedProductIds]);

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('catalog')->__('Name'),
                'index' => 'name',
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
            )
        );


        return parent::_prepareColumns();
    }
}
