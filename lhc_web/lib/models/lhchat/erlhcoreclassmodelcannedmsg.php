<?php

class erLhcoreClassModelCannedMsg
{
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_canned_msg';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
    
    public static $dbSortOrder = 'DESC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'msg' => $this->msg,
            'position' => $this->position,
            'delay' => $this->delay,
            'department_id' => $this->department_id,
            'user_id' => $this->user_id,
            'auto_send' => $this->auto_send,
            'attr_int_1' => $this->attr_int_1,
            'attr_int_2' => $this->attr_int_2,
            'attr_int_3' => $this->attr_int_3,
            'title' => $this->title,
            'explain' => $this->explain,
            'fallback_msg' => $this->fallback_msg
        );
    }

    public function __get($var)
    {
        switch ($var) {
            
            case 'user':
                $this->user = false;
                if ($this->user_id > 0) {
                    try {
                        $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                    } catch (Exception $e) {
                        $this->user = false;
                    }
                }
                return $this->user;
                break;
                
            case 'department':
                $this->department = false;
                if ($this->department_id > 0) {
                    try {
                        $this->department = erLhcoreClassModelDepartament::fetch($this->department_id,true);
                    } catch (Exception $e) {
                        $this->department = false;
                    }
                }
                return $this->department;
                break;
                
            case 'msg_to_user':
                    $this->msg_to_user = str_replace(array_keys($this->replaceData), array_values($this->replaceData), $this->msg);
                    
                    // If not all variables were replaced fallback to fallback message
                    if (preg_match('/\{[a-zA-Z0-9_]+\}/i', $this->msg_to_user))
                    {
                        $this->msg_to_user = str_replace(array_keys($this->replaceData), array_values($this->replaceData), $this->fallback_msg);
                    }
                    
                    return $this->msg_to_user;
                break;
                
            case 'message_title':
                    if ($this->title != '') {
                        $this->message_title = $this->title;
                    } else {
                        $this->message_title = $this->msg_to_user;
                    }
                    return $this->message_title;
                break;
                
            default:
                break;
        }
    }

    public function setReplaceData($replaceData)
    {
        $this->replaceData = $replaceData;
    }

    public static function getCannedMessages($department_id, $user_id, $paramsFilter = array())
    {
        $session = erLhcoreClassChat::getSession();
        $q = $session->createFindQuery('erLhcoreClassModelCannedMsg');
        $filter = array();
        $filter[] = $q->expr->lOr($q->expr->eq('department_id', $q->bindValue($department_id)), $q->expr->lAnd($q->expr->eq('department_id', $q->bindValue(0)), $q->expr->eq('user_id', $q->bindValue(0))), $q->expr->eq('user_id', $q->bindValue($user_id)));
        
        if (isset($paramsFilter['q']) && $paramsFilter['q'] != '') {
            $filter[] = $q->expr->like('msg', $q->bindValue('%' . $paramsFilter['q'] . '%'));
        }
        
        $q->where($filter);
        
        $q->limit(5000, 0);
        $q->orderBy('department_id ASC, position ASC, title ASC');
        $items = $session->find($q);
        
        return $items;
    }

    public static function groupItems($items, $chat, $nameSupport)
    {  
        $replaceArray = array(
            '{nick}' => $chat->nick,
            '{email}' => $chat->email,
            '{phone}' => $chat->phone,
            '{operator}' => $nameSupport
        );
        
        $additionalData = $chat->additional_data_array;
        
        if (is_array($additionalData)) {
            foreach ($additionalData as $row) {
                if (isset($row->identifier) && $row->identifier != '') {
                    $replaceArray['{' . $row->identifier . '}'] = $row->value;
                }
            }
        }
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.canned_message_replace', array(
            'chat' => $chat,
            'replace_array' => & $replaceArray
        ));
        
        $grouped = array();
        
        foreach ($items as $item) {
            
            $item->setReplaceData($replaceArray);
            
            $type = $item->department_id > 0 ? 0 : ($item->user_id > 0 ? 1 : 2);
            $id = $item->department_id > 0 ? $item->department_id : ($item->user_id > 0 ? $item->user_id : 0);
            
            $grouped[$type . '_' . $id][] = $item;
        }
        
        return $grouped;
    }
    
    private $replaceData = array();

    public $id = null;

    public $msg = '';

    public $title = '';

    public $explain = '';

    public $fallback_msg = '';

    public $position = 0;

    public $delay = 0;

    public $department_id = 0;

    public $user_id = 0;

    public $auto_send = 0;

    public $attr_int_1 = 0;

    public $attr_int_2 = 0;

    public $attr_int_3 = 0;
}

?>