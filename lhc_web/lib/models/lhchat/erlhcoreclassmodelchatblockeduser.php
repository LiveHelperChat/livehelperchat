<?php

class erLhcoreClassModelChatBlockedUser
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_blocked_user';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'ip' => $this->ip,
            'user_id' => $this->user_id,
            'datets' => $this->datets,
            'chat_id' => $this->chat_id,
            'dep_id' => $this->dep_id,
            'nick' => $this->nick,
            'btype' => $this->btype,
            'online_user_id' => $this->online_user_id,
            'expires' => $this->expires,
        );
    }

    public function __get($var)
    {
        switch ($var) {
            case 'datets_front':
                return date(erLhcoreClassModule::$dateDateHourFormat, $this->datets);

            case 'expires_front':
                return $this->expires > 0 && $this->expires > time() ? erLhcoreClassChat::formatSeconds($this->expires - time()) : (($this->expires > 0) ? 'Exp.' : '∞');

            case 'block_duration':
                return $this->expires > 0 && $this->expires > time() ? erLhcoreClassChat::formatSeconds($this->expires - $this->datets) : erLhcoreClassChat::formatSeconds(time() - $this->datets);

            case 'user':
                try {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                } catch (Exception $e) {
                    $this->user = '-';
                }
                return $this->user;

            case 'department':
                $this->department = null;
                if ($this->dep_id > 0) {
                    $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                }
                return $this->department;

            default:
                break;
        }
    }

    public function beforeSave()
    {
        $this->datets = time();
    }

    public static function isBlocked($params)
    {
        $db = ezcDbInstance::get();

        $emailBlock = '';

        if (isset($params['email']) && !empty($params['email'])) {
            $emailBlock = ' OR (nick = ' . $db->quote($params['email']) . ' AND btype = 5)';
        }

        if (isset($params['country_code']) && !empty($params['country_code'])) {
            $emailBlock .= ' OR (nick = ' . $db->quote($params['country_code']) . ' AND btype = 6 AND (`dep_id` = ' . $db->quote($params['dep_id']). ' OR `dep_id` = 0))';
        }

        if (isset($params['online_user_id']) && !empty($params['online_user_id'])) {
            $emailBlock .= ' OR (online_user_id = ' . $db->quote($params['online_user_id']) . ' AND btype = 7)';
        }

        $filterBlock = array(
            'customfilter' => array(
                '(
                        (`ip` = ' . $db->quote($params['ip']) .' AND btype IN (0,3,4)) OR 
                        (`nick` = ' . $db->quote($params['nick']) . ' AND btype IN (1,3)) OR 
                        (`nick` = ' . $db->quote($params['nick']) . ' AND `dep_id` = ' . $db->quote($params['dep_id']) . ' AND btype IN (2,4))
                        ' . $emailBlock . '
                    ) AND (expires = 0 OR expires > ' . time() . ')'
            )
        );

        if (isset($params['return_block']) && $params['return_block'] == true) {
            return self::getList($filterBlock);
        }

        $blockRecord = erLhcoreClassModelChatBlockedUser::findOne($filterBlock);

        $isBlocked = $blockRecord instanceof erLhcoreClassModelChatBlockedUser;

        if ($isBlocked == true && isset($params['log_block']) && $params['log_block'] == true) {

            $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
            $data = (array)$auditOptions->data;

            if (isset($data['log_block']) && $data['log_block'] == true) {
                erLhcoreClassLog::write(
                    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'no-site')."\n".
                    print_r($params,true)
                    ,
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'lhc',
                        'category' => 'block',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => $blockRecord->id
                    )
                );
            }

        }

        return $isBlocked;
    }

    public static function blockChat($params) {

        $filter = array();

        $skipStandardBlock = false;

        if (!isset($params['btype']) || $params['btype'] == self::BLOCK_IP) {
            if ((!isset($params['email']) && !isset($params['online_user_id'])) || isset($params['btype'])) {
                $filter = array('filter' => array('btype' => self::BLOCK_IP, 'ip' => $params['chat']->ip));
                $params['btype'] = self::BLOCK_IP;
            } else {
                $skipStandardBlock = true;
            }
        } elseif ($params['btype'] == self::BLOCK_NICK) {
            $filter = array('filter' => array('btype' => self::BLOCK_NICK, 'nick' => (string)$params['chat']->nick));
        } elseif ($params['btype'] == self::BLOCK_NICK_DEP) {
            $filter = array('filter' => array('btype' => self::BLOCK_NICK_DEP,  'dep_id' =>  $params['chat']->dep_id, 'nick' => (string)$params['chat']->nick));
        } elseif ($params['btype'] == self::BLOCK_ALL_IP_NICK_DEP) {
            $filter = array('filter' => array('btype' => self::BLOCK_ALL_IP_NICK_DEP, 'dep_id' =>  $params['chat']->dep_id, 'nick' => (string)$params['chat']->nick));
        } elseif ($params['btype'] == self::BLOCK_ALL_IP_NICK) {
            $filter = array('filter' => array('btype' => self::BLOCK_ALL_IP_NICK, 'nick' => (string)$params['chat']->nick));
        }

        $blockRecordCreated = false;

        if (isset($params['email']) && erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('nick' => (string)$params['email'], 'btype' => self::BLOCK_EMAIL))) == 0) {
            $blockRecordCreated = true;

            // Adjust settings for e-mail blocking
            $paramsEmail = $params;
            $paramsEmail['btype'] = self::BLOCK_EMAIL;
            self::createBlockRecord($paramsEmail);
        }

        if (isset($params['online_user_id']) && $params['online_user_id'] > 0 && erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('online_user_id' => (string)$params['online_user_id'], 'btype' => self::BLOCK_ONLINE_USER))) == 0) {
            $blockRecordCreated = true;

            // Adjust settings for e-mail blocking
            $paramsOnline = $params;
            $paramsOnline['btype'] = self::BLOCK_ONLINE_USER;
            self::createBlockRecord($paramsOnline);
        }

        if ($skipStandardBlock == false && erLhcoreClassModelChatBlockedUser::getCount($filter) == 0)
        {
            self::createBlockRecord($params);
            $blockRecordCreated = true;
        }

        // We want to block only once
        if ($blockRecordCreated == true) {
            self::sendBlockedMessage($params);
        }
    }

    public static function sendBlockedMessage($params) {
        $msg = new erLhcoreClassModelmsg();
        $msg->time = time();
        $msg->user_id = $params['user']->id;
        $msg->chat_id = $params['chat']->id;
        $msg->msg = '';
        $msg->nick = $params['user']->name_support;
        $msg->meta_msg = json_encode([
            'content' => [
                'chat_operation' => [
                    'operation' => 'chat_abort',
                    'ext_args' => json_encode([
                        'attr' => ['chatLiveData','abort'],
                        'data' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','At this moment you can contact us via email only. Sorry for the inconveniences.')
                    ], JSON_HEX_APOS),
                    'intro_op' => '[' . $params['user']->id . '] ' .(string)$params['user']->name_support,
                ]
            ]
        ]);

        \LiveHelperChat\Models\Departments\UserDepAlias::getAlias(array('scope' => 'msg', 'msg' => & $msg, 'chat' => & $params['chat'], 'user_id' => $params['user']->id));
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $params['chat'], 'user_id' => $params['user']->id));
        $msg->saveThis();

        $params['chat']->last_msg_id = $msg->id;
        $params['chat']->last_op_msg_time = time();
        $params['chat']->saveThis();
    }

    public static function createBlockRecord($params) {
        $block = new erLhcoreClassModelChatBlockedUser();
        $block->ip = $params['chat']->ip;
        $block->user_id = erLhcoreClassUser::instance()->getUserID();
        $block->chat_id = $params['chat']->id;
        $block->dep_id = $params['chat']->dep_id;
        $block->nick = (string)($params['btype'] == self::BLOCK_EMAIL ? $params['chat']->email : $params['chat']->nick);
        $block->btype = $params['btype'];
        $block->expires = isset($params['expires']) ? (int)$params['expires'] : 0;
        $block->online_user_id = isset($params['online_user_id']) ? (int)$params['online_user_id'] : 0;
        $block->saveThis();
    }

    const BLOCK_IP = 0;
    const BLOCK_NICK = 1;
    const BLOCK_NICK_DEP = 2;
    const BLOCK_ALL_IP_NICK = 3;
    const BLOCK_ALL_IP_NICK_DEP = 4;
    const BLOCK_EMAIL = 5;
    const BLOCK_COUNTRY = 6;
    const BLOCK_ONLINE_USER = 7;

    public $id = null;
    public $ip = '';
    public $user_id = 0;
    public $datets = null;
    public $chat_id = 0;
    public $dep_id = 0;
    public $nick = '';
    public $btype = 0;
    public $expires = 0;
    public $online_user_id = 0;
}

?>