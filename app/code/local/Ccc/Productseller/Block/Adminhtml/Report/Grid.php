<?php
class Ccc_Productseller_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setTemplate('productseller/report/grid.phtml');
    }
    public function getFilterCollection()
    {
        $urlEncoded = $this->getRequest()->getParam('filter');
        $decodedUrl = base64_decode($urlEncoded);
        $decodedFilters = urldecode($decodedUrl);
        // $url = str_replace('price[currency]=USD&', "", $decodedFilters);
        $filtersArray = explode("&", $decodedFilters);
        $mergeSameFilters = array();
        foreach ($filtersArray as $data) {
            $arr = explode("=", $data);
            $mergeSameFilters[$arr[0]] = $arr[1];
        }
        unset($mergeSameFilters['price[currency]']);
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        if (!empty($mergeSameFilters['entity_id[from]'])) {
            if (!empty($mergeSameFilters['entity_id[to]'])) {
                $collection->addFieldToFilter('entity_id', array('from' => $mergeSameFilters['entity_id[from]'], 'to' => $mergeSameFilters['entity_id[to]']));
            } else {
                $collection->addFieldToFilter('entity_id', ['gteq' => $mergeSameFilters['entity_id[from]']]);
            }
        }
        if (!empty($mergeSameFilters['price[from]'])) {
            if (!empty($mergeSameFilters['price[to]'])) {
                $collection->addFieldToFilter('price', array('from' => $mergeSameFilters['price[from]'], 'to' => $mergeSameFilters['price[to]']));
            } else {
                $collection->addFieldToFilter('price', ['gteq' => $mergeSameFilters['price[from]']]);
            }
        }
        if (!empty($mergeSameFilters['qty[from]'])) {
            if (!empty($mergeSameFilters['qty[to]'])) {
                $collection->addFieldToFilter('qty', array('from' => $mergeSameFilters['qty[from]'], 'to' => $mergeSameFilters['qty[to]']));
            } else {
                $collection->addFieldToFilter('qty', ['gteq' => $mergeSameFilters['qty[from]']]);
            }
        }
        foreach ($mergeSameFilters as $key => $value) {
            if ($key == "type") {
                $collection->addFieldToFilter('type_id', array('like' => "%{$value}%"));
            } else if ($key == "set_name") {
                $collection->addAttributeToFilter('attribute_set_id', array('like' => "%{$value}%"));
            } else if ($key == "price[from]" || $key == 'price[to]' || $key == "entity_id[from]" || $key == 'entity_id[to]' || $key == "qty[from]" || $key == 'qty[to]') {
            } else {
                $collection->addAttributeToFilter($key, array('like' => "%{$value}%"));
            }
        }
        $this->setCollection($collection);
        $this->getCollection()->addWebsiteNamesToResult();
        return parent::_prepareCollection();
    }
    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('filter')) {
            $this->getFilterCollection();
        } else {
            $collection = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('entity_id', null);
            $this->setCollection($collection);
            $this->getCollection()->addWebsiteNamesToResult();
            return parent::_prepareCollection();
        }
    }
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
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

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn(
                'custom_name',
                array(
                    'header' => Mage::helper('catalog')->__('Name in %s', $this->escapeHtml($store->getName())),
                    'index' => 'custom_name',
                )
            );
        }

        $this->addColumn(
            'type',
            array(
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            )
        );

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            array(
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
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

        $store = $this->_getStore();
        $this->addColumn(
            'price',
            array(
                'header' => Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            )
        );

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn(
                'qty',
                array(
                    'header' => Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
                )
            );
        }

        $this->addColumn(
            'visibility',
            array(
                'header' => Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            )
        );
        $this->addColumn(
            'seller_id',
            array(
                'header' => Mage::helper('catalog')->__('Seller ID'),
                'width' => '100px',
                'type'  => 'text',
                'index' => 'seller_id',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                array(
                    'header' => Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                )
            );
        }
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');

        $model = Mage::getModel('Ccc_Productseller/productseller')->getCollection();
        $statuses = array();
        foreach ($model->getData() as $data) {
            $statuses[$data['seller_name']] = $data['seller_name'];
        }

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'seller_id',
            array(
                'label' => Mage::helper('productseller')->__('Change seller Name'),
                'url' => $this->getUrl('*/*/massSeller', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'seller_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('productseller')->__('Active'),
                        'values' => $statuses
                    )
                )
            )
        );

        Mage::dispatchEvent('productseller_adminhtml_report_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
}
