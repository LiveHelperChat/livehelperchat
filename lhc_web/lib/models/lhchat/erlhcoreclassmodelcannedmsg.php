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
            'title' => $this->title,
            'explain' => $this->explain,
            'msg' => $this->msg,
            'fallback_msg' => $this->fallback_msg,
            'position' => $this->position,
            'delay' => $this->delay,
            'department_id' => $this->department_id,
            'user_id' => $this->user_id,
            'auto_send' => $this->auto_send,
            'attr_int_1' => $this->attr_int_1,
            'attr_int_2' => $this->attr_int_2,
            'attr_int_3' => $this->attr_int_3,
            'languages' => $this->languages,
            'additional_data' => $this->additional_data,
            'html_snippet' => $this->html_snippet,
            'unique_id' => $this->unique_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'active_from' => $this->active_from,
            'active_to' => $this->active_to,
            'repetitiveness' => $this->repetitiveness,
            'days_activity' => $this->days_activity,
            'disabled' => $this->disabled,
            'delete_on_exp' => $this->delete_on_exp,
        );
    }

    public function beforeSave($params = array())
    {
        if ($this->created_at == 0) {
            $this->created_at = time();
        }

        $this->updated_at = time();
    }

    public function __toString(){
        return $this->title;
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

            case 'subject_name_front':
                $this->subject_name_front = [];
                $subjects = erLhcoreClassModelCannedMsgSubject::getList(array('filter' => array('canned_id' => $this->id)));
                foreach ($subjects as $subject) {
                    $this->subject_name_front[] = (string)$subject->subject;
                }
                return $this->subject_name_front;

            case 'department_ids_front':
                $this->department_ids_front = [];
                if ($this->id > 0) {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT `dep_id` FROM `lh_canned_msg_dep` WHERE `canned_id` = " . $this->id);
                    $stmt->execute();
                    $this->department_ids_front = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }
                return $this->department_ids_front;

                
            case 'msg_to_user':
                    $this->msg_to_user = str_replace(array_keys($this->replaceData), array_values($this->replaceData), $this->msg);
                    
                    // If not all variables were replaced fallback to fallback message
                    if (preg_match('/\{[a-zA-Z0-9_]+\}/i', $this->msg_to_user))
                    {
                        $this->msg_to_user = str_replace(array_keys($this->replaceData), array_values($this->replaceData), $this->fallback_msg);
                    }

                    if ($this->html_snippet != '') {
                        $this->msg_to_user .= '[html_snippet]'.$this->id.'[/html_snippet]';
                    }

                    return $this->msg_to_user;

            case 'message_title':
                    if ($this->title != '') {
                        $this->message_title = $this->title;
                    } else {
                        $this->message_title = $this->msg_to_user;
                    }
                    return $this->message_title;

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

            case 'days_activity_array':
            case 'additional_data_array':
                $varSystem = str_replace('_array','', $var);
                $jsonData = json_decode($this->{$varSystem},true);
                if ($jsonData !== null) {
                    $this->$var = $jsonData;
                } else {
                    $this->$var = $this->$varSystem;
                }

                if (!is_array($this->$var)) {
                    $this->$var = array();
                }

                return $this->$var;

            case 'created_at_front':
            case 'updated_at_front':
                $var = str_replace('_front','', $var);
                $this->{$var . '_front'} = date('Ymd') == date('Ymd',$this->{$var}) ? date(erLhcoreClassModule::$dateHourFormat,$this->{$var}) : date(erLhcoreClassModule::$dateDateHourFormat,$this->{$var});
                return $this->{$var . '_front'};

            default:
                break;
        }
    }

    public function afterSave()
    {
        if ($this->unique_id == 0) {
            $this->unique_id = $this->id;
            $this->updateThis(array('update' => array('unique_id')));
        }

        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lh_canned_msg_dep` WHERE `canned_id` = :canned_id');
        $stmt->bindValue(':canned_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();

        if (isset($this->department_ids) && !empty($this->department_ids)) {
           $values = [];
           foreach ($this->department_ids as $department_id) {
               $values[] = "(" . $this->id . "," . $department_id . ")";
           }
           if (!empty($values)) {
               $db->query('INSERT INTO `lh_canned_msg_dep` (`canned_id`,`dep_id`) VALUES ' . implode(',',$values));
           }
        }

        $tagLinks = erLhcoreClassModelCannedMsgTagLink::getList(array('filter' => array('canned_id' => $this->id)));
                
        $tagsArray = array();
        $tagsArrayObj = array();
        $tagsArrayLinkId = array();
                
        $tags = array();
        foreach (array_unique(explode(',', strtolower($this->tags_plain))) as $tagKeyword) {
            $tags[] = trim($tagKeyword);
        }
        
        $tags = array_filter(array_unique($tags));

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
        
        $this->tags = $tagsArrayObj;
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

        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lh_canned_msg_dep` WHERE `canned_id` = :canned_id');
        $stmt->bindValue(':canned_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();
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
	        $filter[] = $q->expr->lOr(
                $q->expr->lAnd(
                    $q->expr->eq('department_id', $q->bindValue(0)),
                    $q->expr->eq('user_id', $q->bindValue(0))
                ),
                $q->expr->lAnd( // Support old settings
                    $q->expr->eq('department_id', $q->bindValue($department_id)),
                    $q->expr->eq('user_id', $q->bindValue(0))
                ),
                $q->expr->eq('user_id', $q->bindValue($user_id)),
                'id IN (SELECT canned_id FROM lh_canned_msg_dep WHERE dep_id = ' . (int)$department_id . ')'
            );
	
	        if (isset($paramsFilter['q']) && $paramsFilter['q'] != '') {
	            $filter[] = $q->expr->lOr(
	                $q->expr->like('msg', $q->bindValue('%' . $paramsFilter['q'] . '%')),
	                $q->expr->like('title', $q->bindValue('%' . $paramsFilter['q'] . '%'))
                );
	        }

	        if (isset($paramsFilter['id']) && !empty($paramsFilter['id'])) {
	            $filter[] = $q->expr->in('id', $paramsFilter['id']);
	        }

            $filter[] = $q->expr->eq('disabled', 0);

            $dayShort = array(
                1 => 'mod',
                2 => 'tud',
                3 => 'wed',
                4 => 'thd',
                5 => 'frd',
                6 => 'sad',
                7 => 'sud'
            );

            $filter[] = "(
                repetitiveness = 0 OR 
                (repetitiveness = 1 AND days_activity != '' AND JSON_EXTRACT(days_activity,'$.".$dayShort[date('N')].".start') <= " . date('Hi') . " AND JSON_EXTRACT(days_activity,'$.".$dayShort[date('N')].".end') >= " . date('Hi') . " ) OR
                (repetitiveness = 2 AND active_from <= " . time() . " AND (active_to = 0 OR active_to >= " . time() . ")) OR
                (repetitiveness = 3 AND FROM_UNIXTIME(active_from,'%m%d%H%i') <= " . date('mdHi') . " AND FROM_UNIXTIME(active_to,'%m%d%H%i') >= " . date('mdHi') . ")
            )";

	        $q->where($filter);
	       
	        $q->limit(50, 0);
	        $q->orderBy('position ASC, title ASC');
	        $items = $session->find($q);
        }

        // Include subjects associated with canned messages
        $keys = array_keys($items);
        if (!empty($keys)) {
            $cannedSubjects = erLhcoreClassModelCannedMsgSubject::getList(array('limit' => false, array('filterin' => array('canned_id' => $keys))));
            foreach ($cannedSubjects as $cannedSubject) {
                if (isset($items[$cannedSubject->canned_id])){
                    if (!isset($items[$cannedSubject->canned_id]->subjects_ids)) {
                        $items[$cannedSubject->canned_id]->subjects_ids = [];
                    }
                    $items[$cannedSubject->canned_id]->subjects_ids[] = $cannedSubject->subject_id;
                }
            }
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

        \LiveHelperChat\Models\Departments\UserDepAlias::getAlias(array('scope' => 'canned_replace', 'replace_array' => & $replaceArray, 'chat' => $chat, 'user' => $user));
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.canned_message_replace', array(
            'chat' => $chat,
            'replace_array' => & $replaceArray,
            'user' => $user,
            'items' => & $items
        ));
        
        $grouped = array();

        $replaceCustomArgs = [];
        foreach ($items as $item) {
            // Set proper message by language
            $item->setMessageByChatLocale($chat->chat_locale);

            foreach (['msg','fallback_msg'] as $metaMsg) {
                $matchesMessage = [];
                preg_match_all('/\{[A-Za-z0-9\_]+\}/is',$item->{$metaMsg}, $matchesMessage);
                if (isset($matchesMessage[0]) && !empty($matchesMessage[0])) {
                    foreach ($matchesMessage[0] as $replaceItem) {
                        if (key_exists($replaceItem,$replaceArray) == false) {
                            $replaceCustomArgs[] = $replaceItem;
                        }
                    }
                }
            }
        }

        $replaceCustomArgs = array_unique($replaceCustomArgs);

        if (!empty($replaceCustomArgs)) {

            $identifiers = [];
            $identifiersApplied = [];
            foreach ($replaceCustomArgs as $replaceArg) {
                $identifiers[] = str_replace(['{','}'],'', $replaceArg);
            }

            $replaceRules = erLhcoreClassModelCannedMsgReplace::getList(array(
                'sort' => 'repetitiveness DESC', // Default translation will be the last one if more than one same identifier is found
                'limit' => false,
                'filterin' => array('identifier' => $identifiers))
            );

            foreach ($replaceRules as $replaceRule) {
                if ($replaceRule->is_active && !in_array($replaceRule->identifier,$identifiersApplied)) {
                    $replaceArray['{' . $replaceRule->identifier . '}'] = $replaceRule->getValueReplace(['chat' => $chat, 'user' => $user]);
                    $identifiersApplied[] = $replaceRule->identifier;
                }
            }
        }

        foreach ($items as $item) {

            // Set replace data
            $item->setReplaceData($replaceArray);


            if (strpos($item->msg, '{args.') !== false) {
                $matchesValues = array();
                preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $item->msg, $matchesValues);
                if (!empty($matchesValues[0])) {
                    foreach ($matchesValues[0] as $indexElement => $elementValue) {
                        $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array('user' => $user, 'chat' => $chat), $matchesValues[1][$indexElement], '.');
                        $item->msg = str_replace($elementValue, $valueAttribute['found'] == true ? $valueAttribute['value'] : '', $item->msg);
                    }
                }
            }

            $type = $item->department_id > 0 ? 0 : ($item->user_id > 0 ? 1 : 2);
            $id = $item->department_id > 0 ? $item->department_id : ($item->user_id > 0 ? $item->user_id : 0);
            
            $grouped[$type . '_' . $id][] = $item;
        }
        
        return $grouped;
    }

    /**
     * @desc Finds message in proper locale if it exists
     *
     * @param $locale
     */
    public function setMessageByChatLocale($locale) {
        if ($locale != '' && $this->languages != '') {
            $languages = json_decode($this->languages, true);

            if (is_array($languages)) {
                foreach ($languages as $data) {
                    if (in_array($locale, $data['languages'])) {

                        if ($data['message'] != '') {
                            $this->msg = $data['message'];
                        }

                        if ($data['fallback_message'] != '') {
                            $this->fallback_msg = $data['fallback_message'];
                        }
                        return ;
                    }
                }
            }
        }
    }

    CONST REP_NO = 0;
    CONST REP_DAILY = 1;
    CONST REP_PERIOD = 2;
    CONST REP_PERIOD_REP = 3;

    private $replaceData = array();

    public $id = null;
    public $msg = '';
    public $title = '';
    public $explain = '';
    public $languages = '';
    public $fallback_msg = '';
    public $additional_data = '';
    public $html_snippet = '';
    public $position = 0;
    public $delay = 0;
    public $department_id = 0;
    public $department_ids = [];
    public $user_id = 0;
    public $auto_send = 0;
    public $attr_int_1 = 0;
    public $attr_int_2 = 0;
    public $attr_int_3 = 0;
    public $unique_id = 0;

    public $created_at = 0;
    public $updated_at = 0;
    public $active_from = 0;
    public $active_to = 0;
    public $repetitiveness = self::REP_NO;
    public $days_activity = '';
    public $disabled = 0;
    public $delete_on_exp = 0;


}

?>