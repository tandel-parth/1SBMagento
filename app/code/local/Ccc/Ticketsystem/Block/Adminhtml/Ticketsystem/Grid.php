<?php
class Ccc_Ticketsystem_Block_Adminhtml_Ticketsystem_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        $this->setTemplate('ticketsystem/grid.phtml');
    }

    public function getStatusName($statusId)
    {
        return Mage::getModel('ccc_ticketsystem/status')->load($statusId);
    }

    public function getAdminName($adminId)
    {
        return Mage::getModel('admin/user')->load($adminId);
    }

    public function getTableData($currentPage, $pageSize)
    {
        // Get the filters parameter as a JSON string
        $filtersId = $this->getRequest()->getParam('filter_id');
        $collection = Mage::getModel('ccc_ticketsystem/ticketsystem')->getCollection();
        $filterCollection = Mage::getModel('ccc_ticketsystem/filter')->load($filtersId)->getFilterData();
        $filters = json_decode($filterCollection, true); // Decode as associative array

        // Apply filters if they are provided
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $collection->addFieldToFilter($key, ['in' => $value]);
                } else if ($key == 'created_at') {
                    $createdAt = date('Y-m-d', strtotime('-' . $value . ' days'));
                    $collection->addFieldToFilter('created_at', ['gteq' => $createdAt]);
                } else if ($key == 'user') {
                    $userId = $value;
                    $ticketIds = $collection->getAllIds();
                    $commentCollection = Mage::getModel('ccc_ticketsystem/comment')->getCollection();
                    // Add subquery for latest comments per ticket
                    $subSelect = $commentCollection->getConnection()
                        ->select()
                        ->from(
                            array('ccc' => $commentCollection->getTable('ccc_ticketsystem/comment')),
                            array('ticket_id', 'max_created_at' => new Zend_Db_Expr('MAX(created_at)'))
                        )
                        ->where('ccc.ticket_id IN (?)', $ticketIds)
                        ->group('ccc.ticket_id');

                    $commentCollection->getSelect()
                        ->joinInner(
                            array('max_comments' => $subSelect),
                            'main_table.ticket_id = max_comments.ticket_id AND main_table.created_at = max_comments.max_created_at',
                            array()
                        );

                    $commentCollection->addFieldToFilter('user_id', $userId);
                    $commentData = $commentCollection->getData();
                    $ticketIds = array();
                    foreach ($commentData as $data) {
                        $ticketIds[] = $data['ticket_id'];
                    }
                    $collection->addFieldToFilter('ticket_id', ['in' => $ticketIds]);
                }
            }
        }

        $collection->setCurPage($currentPage);
        $collection->setPageSize($pageSize);

        return $collection->getData();
    }
}
