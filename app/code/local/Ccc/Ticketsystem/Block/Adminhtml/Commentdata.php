<?php
class Ccc_Ticketsystem_Block_Adminhtml_Commentdata extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('ticketsystem/commentdata.phtml');
    }
    public function getTicket()
    {
        $id = $this->getRequest()->getParam('id');
        return $id;
    }
    public function organizeComments($comments, $maxLevel = 2, $parentLevel = 0)
    {
        $commentMap = [];
        $rootComments = [];

        foreach ($comments as &$comment) {
            $comment['children'] = [];
            $commentMap[$comment['comment_id']] = &$comment;
        }

        foreach ($comments as &$comment) {
            if ($comment['parent_id'] == 0) {
                $rootComments[] = &$comment;
            } else if (isset($commentMap[$comment['parent_id']])) {
                $commentMap[$comment['parent_id']]['children'][] = &$comment;
            }
        }

        $filteredComments = $this->filterCommentsByLevel($rootComments, $maxLevel);
        return $filteredComments;
    }


    private function filterCommentsByLevel($comments, $maxLevel, $currentLevel = 1)
    {
        $filtered = [];

        foreach ($comments as $comment) {
            $filteredComment = $comment;

            // Recursively filter children if not at maximum level
            if ($currentLevel > $maxLevel && !empty($comment['children'])) {
                $filteredComment['children'] = $this->filterCommentsByLevel($comment['children'], $maxLevel, $currentLevel + 1);
            } else {
                $filteredComment['children'] = []; // Clear children if at maximum level
            }

            $filtered[] = $filteredComment;
        }
        foreach ($filtered as &$comment) {
            $comment['rowspan'] = $this->countDescendants($comment) + 1;
        }

        return $filtered;
    }

    public function getComments($ticketId, $status)
    {
        $collection = Mage::getModel('ccc_ticketsystem/comment')
            ->getCollection()
            ->addFieldToFilter('ticket_id', $ticketId);
        if ($status !== 'all') {
            $collection->addFieldToFilter('status', $status);
        }
        $data = $collection->getData();
        return $data;
    }

    private function countDescendants($comment)
    {
        $count = 0;
        foreach ($comment['children'] as $child) {
            $count += 1 + $this->countDescendants($child);
        }
        return $count;
    }
    public function currentIsLocked($parentId)
    {
        $id = $this->getTicket();
        $isLock = 0;
        $commentCollection = Mage::getModel('ccc_ticketsystem/comment')->getCollection()
            ->addFieldToFilter('ticket_id', $id)
            ->addFieldToFilter('comment_id', $parentId)->getFirstItem();
        return $commentCollection->getIsLock();
    }
    public function generateCommentTable($comments, $maxLevel = 0, $isRoot = true, $isLast = true)
    {
        $html = '';
        $secondLastLevelLocked = $this->isSecondLastLevelLocked($comments, $this->getMaxLevel() - 1);
        foreach ($comments as $comment) {
            $html .= "<tr>";
            if ($comment['parent_id'] == 0 && $comment['level'] !== '0') {
                $html .= "<td colspan='{$comment['level']}' rowspan='{$comment['rowspan']}'><span data-comment-id='{$comment['comment_id']}'></span></td>";
            }
            $html .= "<td rowspan='{$comment['rowspan']}'>";
            $description = html_entity_decode(strip_tags($comment['description']));
            $html .= "<span class='comment-content' data-comment-id='{$comment['comment_id']}' data-parent-id='{$comment['parent_id']}' data-level='{$comment['level']}'>{$description}</span>";
            if ($comment['status'] == 'complete') {
                $html .= "<span style='color:green'>{$comment['status']} ✅</span>";
            } else {
                if ($this->currentIsLocked($comment['parent_id']) != 0 && $this->currentIsLocked($comment['comment_id']) == 0) {
                    $html .= " <button class='complete-button' data-comment-id='{$comment['comment_id']}' data-url='{$this->getUrl('*/*/updateStatus')}' onclick='completeComment(this)'>Complete</button>";
                    $html .= " <button class='add-reply-btn' data-ticket='{$this->getTicket()}' data-comment-id='{$comment['comment_id']}' data-parent-id='{$comment['parent_id']}' data-url='{$this->getUrl('*/*/saveComment')}' onclick='addReply(this)'>Reply</button>";
                } else {
                    if ($isRoot && $comment['is_lock'] == 0) {
                        $html .= " <button class='complete-button' data-comment-id='{$comment['comment_id']}' data-url='{$this->getUrl('*/*/updateStatus')}' onclick='completeComment(this)'>Complete</button>";
                        $html .= " <button class='add-reply-btn' data-ticket='{$this->getTicket()}' data-comment-id='{$comment['comment_id']}' data-parent-id='{$comment['parent_id']}' data-url='{$this->getUrl('*/*/saveComment')}' onclick='addReply(this)'>Reply</button>";
                    }
                }
            }
            $html .= "</td>";
            $html .= "</tr>";
            if (!empty($comment['children'])) {
                $html .= $this->generateCommentTable($comment['children'], $maxLevel, false, false);
            }
        }
        if ($isRoot && $isLast) {
            if (!$secondLastLevelLocked) {
                $maxLevel--;
            }
            $html .= "<tr>";
            if (!$maxLevel) {
                $html .= "<td colspan='{$maxLevel}' class='comment-add' style='display:none'></td>";
            } else {
                $html .= "<td colspan='{$maxLevel}' class='comment-add'></td>";
            }
            $html .= "<td><button data-ticket='{$this->getTicket()}' data-url='{$this->getUrl('*/*/saveComment')}' data-level='{$maxLevel}' onclick='addQuestions(this)'>Add Questions</button></td>";
            $html .= "</tr>";
            $html .= "<tr class='lock-container' style='display:none'>";
            $html .= "<td colspan='{$maxLevel}' class='colspan-lock' style='display:none'>Lock button:</td>";
            $html .= "<td>";
            $html .= "<button class='lock' data-ticket='{$this->getTicket()}' data-url='{$this->getUrl('*/*/lockUpdate')}' data-level='{$maxLevel}' onclick='lockComment(this, \"{$this->getUrl('*/*/commentdata')}\")'>Lock</button>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        return $html;
    }
    private function isSecondLastLevelLocked($comments, $secondLastLevel)
    {
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == 0) {
                return true;
            } else {
                if ($comment['level'] == $secondLastLevel && $comment['is_lock'] != 0) {
                    return true;
                }
                if (!empty($comment['children'])) {
                    if ($this->isSecondLastLevelLocked($comment['children'], $secondLastLevel)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }



    public function getMaxLastLevel($comments)
    {
        $maxLastLevel = 0;
        foreach ($comments as $comment) {
            $lastChildLevel = $this->getLastChildLevel($comment);

            if ($lastChildLevel > $maxLastLevel) {
                $maxLastLevel = $lastChildLevel;
            }
        }

        return $maxLastLevel;
    }

    public function getLastChildLevel($comment)
    {
        $maxChildLevel = $comment['level'];
        if (!empty($comment['children'])) {
            foreach ($comment['children'] as $child) {
                $childLevel = $this->getLastChildLevel($child);
                if ($childLevel > $maxChildLevel) {
                    $maxChildLevel = $childLevel;
                }
            }
        }

        return $maxChildLevel;
    }
}
