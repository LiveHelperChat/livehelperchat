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
                
            case 'tags':
                    $this->tags = array();
                    
                    if ($this->id > 0) {
                        $this->tags = erLhcoreClassModelCannedMsgTag::getList(array('innerjoin' => array('lh_canned_msg_tag_link' => array('lh_canned_msg_tag_link.tag_id','lh_canned_msg_tag.id')),'filter' => array('lh_canned_msg_tag_link.canned_id' => $this->id)));
                    }
                    
                    return $this->tags;
                break; 
                   
            case 'tags_plain':
                    $tagsPlain = array();
                    foreach ($this->tags as $tag) {
                        $tagsPlain[] = $tag->tag;
                    }
                    $this->tags_plain = implode(', ', $tagsPlain);
                    return $this->tags_plain;
                break;
                    
            default:
                break;
        }
    }

    public function afterSave()
    {            
        $tagLinks = erLhcoreClassModelCannedMsgTagLink::getList(array('filter' => array('canned_id' => $this->id)));
                
        $tagsArray = array();
        $tagsArrayObj = array();
        $tagsArrayLinkId = array();
                
        $tags = array();
        foreach (array_unique(explode(',', strtolower($this->tags_plain))) as $tagKeyword) {
            $tags[] = trim($tagKeyword);
        }
        
        $tags = array_unique($tags);
        
        foreach ($tags as $tagKeyword) {
            $tagKeywordTrimmed = trim($tagKeyword);
            
            $tag = erLhcoreClassModelCannedMsgTag::findOne(array('filter' => array('tag' => $tagKeywordTrimmed)));
            
            if (!($tag instanceof erLhcoreClassModelCannedMsgTag)) {                   
                $tag = new erLhcoreClassModelCannedMsgTag();
                $tag->tag = $tagKeywordTrimmed;
                $tag->saveThis();
            }
            
            $tagLink = erLhcoreClassModelCannedMsgTagLink::findOne(array('filter' => array('tag_id' => $tag->id, 'canned_id' => $this->id)));
            
            if (!($tagLink instanceof erLhcoreClassModelCannedMsgTagLink)) {
                $tagLink = new erLhcoreClassModelCannedMsgTagLink();
                $tagLink->tag_id = $tag->id;
                $tagLink->canned_id = $this->id;
                $tagLink->saveThis();
            }
            
            // Update number of assigned canned messages to specific tag
            $tag->cnt = erLhcoreClassModelCannedMsgTagLink::getCount(array('filter' => array('tag_id' => $tag->id)));
            $tag->saveThis();
            
            $tagsArrayLinkId[] = $tagLink->id;
            $tagsArrayObj[] = $tag;
            $tagsArray[] = $tag->tag;
        }
        
        $linksToRemove = array_diff(array_keys($tagLinks), $tagsArrayLinkId);
        
        if (!empty($linksToRemove)) {
            $tagLinks = erLhcoreClassModelCannedMsgTagLink::getList(array('filterin' => array('id' => $linksToRemove)));
            foreach ($tagLinks as $tagLink) {
                $tagLink->removeThis();
                
                // It does not have any more associated shortucts to keyword, we can remove tag itself
                if (erLhcoreClassModelCannedMsgTagLink::getCount(array('filter' => array('tag_id' => $tagLink->tag_id))) == 0) {
                    $tag = erLhcoreClassModelCannedMsgTag::fetch($tagLink->tag_id);
                    if ($tag instanceof erLhcoreClassModelCannedMsgTag) {
                        $tag->removeThis();
                    }
                }
            }
        }
        
        $this->tags = $tag;
        $this->tags_plain = implode(', ', $tagsArray);        
    }
    
    public function afterRemove()
    {
        $tagLinks = erLhcoreClassModelCannedMsgTagLink::getList(array('filter' => array('canned_id' => $this->id)));
        
        foreach ($tagLinks as $tagLink) {
            $tagLink->removeThis();
        
            // It does not have any more associated shortucts to keyword, we can remove tag itself
            if (erLhcoreClassModelCannedMsgTagLink::getCount(array('filter' => array('tag_id' => $tagLink->tag_id))) == 0) {
                $tag = erLhcoreClassModelCannedMsgTag::fetch($tagLink->tag_id);
                if ($tag instanceof erLhcoreClassModelCannedMsgTag) {
                    $tag->removeThis();
                }
            }
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
        $items = array();
        
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.canned_message_filter', array(
        		'params_filter' => $paramsFilter,
        		'filter' => & $filter,
        		'department_id' => $department_id,
        		'user_id' => $user_id,
        		'q' => $q,
        		'items' => & $items,
        		'session' => & $session
        ));
        
        if ($response === false) {
	        // Extension did not changed any filters, use default        
	        $filter[] = $q->expr->lOr($q->expr->eq('department_id', $q->bindValue($department_id)), $q->expr->lAnd($q->expr->eq('department_id', $q->bindValue(0)), $q->expr->eq('user_id', $q->bindValue(0))), $q->expr->eq('user_id', $q->bindValue($user_id)));
	
	        if (isset($paramsFilter['q']) && $paramsFilter['q'] != '') {
	            $filter[] = $q->expr->like('msg', $q->bindValue('%' . $paramsFilter['q'] . '%'));
	        }

	        if (isset($paramsFilter['id']) && !empty($paramsFilter['id'])) {
	            $filter[] = $q->expr->in('id', $paramsFilter['id']);
	        }
	        
	        $q->where($filter);
	       
	        $q->limit(5000, 0);
	        $q->orderBy('position ASC, title ASC');
	        $items = $session->find($q);
        }
        
        return $items;
    }
    
    public static function groupItems($items, $chat, $user)
    {  
        $replaceArray = array(
            '{nick}' => $chat->nick,
            '{email}' => $chat->email,
            '{phone}' => $chat->phone,
            '{operator}' => $user->name_support
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
            'replace_array' => & $replaceArray,
            'user' => $user
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