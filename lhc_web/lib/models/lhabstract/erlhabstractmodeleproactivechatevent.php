<?php

class erLhAbstractModelProactiveChatEvent
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_event';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'vid_id' => $this->vid_id,
            'ev_id' => $this->ev_id,
            'ts' => $this->ts,
            'val' => $this->val
        );
        
        return $stateArray;
    }

    public function __toString()
    {
        return $this->ev_id;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodeleproactivechatevent.php');
    }

    public function getModuleTranslations()
    {
        /**
         * Get's executed before permissions check.
         * It can redirect to frontpage throw permission exception etc
         */
        $metaData = array(
            'permission_delete' => array(
                'module' => 'lhchat',
                'function' => 'administrateinvitations'
            ),
            'permission' => array(
                'module' => 'lhchat',
                'function' => 'administrateinvitations'
            ),
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Pro active chat events')
        );
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_proactive', array(
            'object_meta_data' => & $metaData
        ));
        
        return $metaData;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'ev':
                    $this->ev = erLhAbstractModelProactiveChatVariables::fetch($this->ev_id);
                    return $this->ev;
                break;
                
            case 'left_menu':
                $this->left_menu = '';
                return $this->left_menu;
                break;
            
            default:
                break;
        }
    }

    public $id = null;

    public $vid_id = 0;

    public $ev_id = 0;

    public $ts = 0;

    public $val = '';

    public $hide_add = false;

    public $hide_delete = false;
}

?>