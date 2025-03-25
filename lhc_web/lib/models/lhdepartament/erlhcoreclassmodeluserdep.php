<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelUserDep
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_userdep';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'dep_id' => $this->dep_id,
            'last_activity' => $this->last_activity,
            'hide_online' => $this->hide_online,
            'last_accepted' => $this->last_accepted,
            'last_accepted_mail' => $this->last_accepted_mail,
            'active_chats' => $this->active_chats,
            'pending_chats' => $this->pending_chats,
            'inactive_chats' => $this->inactive_chats,
            'active_mails' => $this->active_mails,
            'pending_mails' => $this->pending_mails,
            'hide_online_ts' => $this->hide_online_ts,
            'always_on' => $this->always_on,
            'lastd_activity' => $this->lastd_activity,
            'ro' => $this->ro,
            'type' => $this->type,
            'dep_group_id' => $this->dep_group_id,
            'exclude_autoasign' => $this->exclude_autoasign,
            'exclude_autoasign_mails' => $this->exclude_autoasign_mails,
            'exc_indv_autoasign' => $this->exc_indv_autoasign,
            'max_chats' => $this->max_chats,
            'max_mails' => $this->max_mails,
            'assign_priority' => $this->assign_priority,
            'chat_min_priority' => $this->chat_min_priority,
            'chat_max_priority' => $this->chat_max_priority,

        );
    }

    public function __get($var)
    {
        switch ($var) {
            case 'user':
                $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                return $this->user;

            case 'live_chats':
                $this->live_chats = $this->active_chats + $this->pending_chats - $this->inactive_chats;
                return $this->live_chats;

            case 'free_slots':
                $this->free_slots = $this->max_chats - $this->live_chats;
                return $this->free_slots;

            case 'lastactivity_ago':
                $this->lastactivity_ago = erLhcoreClassChat::getAgoFormat($this->last_activity);
                return $this->lastactivity_ago;

            case 'lac_ago_s':
                $this->lac_ago_s = time() - $this->last_activity;
                return $this->lac_ago_s;

            case 'last_accepted_ago':
                $this->last_accepted_ago = erLhcoreClassChat::getAgoFormat($this->last_accepted);
                return $this->last_accepted_ago;

            case 'offline_since_s':
                $this->offline_since_s = null;
                if ($this->hide_online == 1 && $this->hide_online_ts > 0) {
                    $diff = time() - $this->hide_online_ts;
                    if ($diff <= 60) {
                        $this->offline_since_s = ['i' => '10', 'c' => '#90EF90'];
                    } elseif ($diff > 60 && $diff <= 120) {
                        $this->offline_since_s = ['i' => '20', 'c' => '#B0F5AB'];
                    } elseif ($diff > 120 && $diff <= 360) {
                        $this->offline_since_s = ['i' => '40', 'c' => '#CDFFCC'];
                    } elseif ($diff > 360 && $diff <= 600) {
                        $this->offline_since_s = ['i' => '60', 'c' => '#FFCCCB'];
                    } elseif ($diff > 600 && $diff <= 900) {
                        $this->offline_since_s = ['i' => '80', 'c' => '#FC94A1'];
                    } elseif ($diff > 900 && $diff < 3600) {
                        $this->offline_since_s = ['i' => '90', 'c' => '#FC6C85'];
                    }
                }
                return $this->offline_since_s;

            case 'offline_since':
                $this->offline_since = erLhcoreClassChat::getAgoFormat($this->hide_online_ts);
                return $this->offline_since;

            case 'avatar':
                if ($this->user->has_photo) {
                    $this->avatar = $this->user->photo_path;
                } elseif ($this->user->avatar != '') {
                    $this->avatar = erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar') . '/' . $this->user->avatar;
                } else {
                    $this->avatar = null;
                }
                return $this->avatar;

            case 'name_support':
                $this->name_support = $this->user->name_support;
                return $this->name_support;

            case 'name_official':
                $this->name_official = $this->user->name_official;
                return $this->name_official;

            case 'departments_names':
                $this->departments_names = array();
                $ids = $this->user->departments_ids;

                if ($ids != '') {
                    $parts = explode(',', $ids);
                    sort($parts);

                    if (!empty($this->dep_id_filter)) {
                        $parts = array_intersect($parts,$this->dep_id_filter);
                    }

                    $totalAssigned = count($parts);

                    if ($totalAssigned > 4) {
                        $this->departments_names[] = '['.$totalAssigned.' d.]';
                    }

                    $parts = array_splice($parts,0,4);

                    foreach ($parts as $depId) {
                        if ($depId == 0) {
                            $this->departments_names[] = '∞';
                        } elseif ($depId > 0) {
                            try {
                                $dep = erLhcoreClassModelDepartament::fetch($depId, true);
                                if (is_object($dep)) {
                                    $this->departments_names[] = $dep->name;
                                }
                            } catch (Exception $e) {

                            }
                        }
                    }
                }
                return $this->departments_names;

            default:
                break;
        }
    }

    public static function getOnlineOperators($currentUser, $canListOnlineUsersAll = false, $params = array(), $limit = 10, $onlineTimeout = 120, $paramsExecution = array())
    {
        $userData = $currentUser->getUserData(true);
        $filter = array();

        if ($userData->all_departments == 0 && $canListOnlineUsersAll == false) {
            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID(), $userData->cache_version);

            if (count($userDepartaments) == 0) return array();

            $index = array_search(-1, $userDepartaments);
            if ($index !== false) {
                unset($userDepartaments[$index]);
            }

            if (count($userDepartaments) == 0) return array();

            $filter['customfilter'][] = '(dep_id IN (' . implode(',', $userDepartaments) . ') OR user_id = ' . $currentUser->getUserID() . ')';
        };

        if (isset($paramsExecution['dashboard']) && $paramsExecution['dashboard'] === true){
            $filter['customfilter'][] = '(last_activity > ' . (int)(time() - $onlineTimeout) . ')';
        } else {
            $filter['customfilter'][] = '(last_activity > ' . (int)(time() - $onlineTimeout) . ' OR `lh_userdep`.`always_on` = 1)';
        }

        $filter['innerjoin'] = array('`lh_users`' => array('`lh_userdep`.`user_id`', '`lh_users`.`id`'));

        $filter['limit'] = $limit;

        if (!isset($params['sort'])) {
            $filter['sort'] = 'active_chats DESC, name ASC';
        }

        $filter['group'] = 'user_id';

        $filter = array_merge_recursive($filter, $params);

        $filter['ignore_fields'] = array('chat_max_priority','chat_min_priority','assign_priority', 'max_mails','last_accepted_mail','exc_indv_autoasign','exclude_autoasign_mails','active_mails','pending_mails','exclude_autoasign','max_chats','dep_group_id','type','ro','id','dep_id','hide_online_ts','hide_online','last_activity','lastd_activity','always_on','last_accepted','active_chats','pending_chats','inactive_chats','ro');

        $filter['select_columns'] = '
         max(`lh_userdep`.`id`) as `id`, 
        max(`ro`) as `ro`,
        max(`max_chats`) as `max_chats`,
        max(`max_mails`) as `max_mails`,
        max(`dep_id`) as `dep_id`,
        max(`hide_online_ts`) as `hide_online_ts`,
        max(`lh_userdep`.`hide_online`) as `hide_online`,
        max(`last_activity`) as `last_activity`, 
        max(`lastd_activity`) as `lastd_activity`, 
        max(`lh_userdep`.`always_on`) as `always_on`, 
        max(`last_accepted`) as `last_accepted`,
        max(`last_accepted_mail`) as `last_accepted_mail`,
        max(`active_chats`) as `active_chats`,
        max(`pending_chats`) as `pending_chats`,
        max(`inactive_chats`) as `inactive_chats`,
        max(`active_mails`) as `active_mails`,
        max(`pending_mails`) as `pending_mails`,
        min(`ro`) as `ro`';

        $list = self::getList($filter);

        if (isset($userDepartaments)) {
            $userDepartaments[] = 0;
            foreach ($list as & $listItem) {
                $listItem->dep_id_filter = $userDepartaments;
            }
        }

        return $list;
    }

    public $id = null;
    public $user_id = 0;
    public $dep_id = 0;
    public $hide_online_ts = 0;
    public $hide_online = 0;
    public $last_activity = 0;
    public $lastd_activity = 0;
    public $last_accepted = 0;
    public $last_accepted_mail = 0;
    public $active_chats = 0;
    public $pending_chats = 0;
    public $inactive_chats = 0;
    public $active_mails = 0;
    public $pending_mails = 0;
    public $always_on = 0;
    public $ro = 0;
    public $type = 0;
    public $dep_group_id = 0;
    public $exclude_autoasign = 0;
    public $exclude_autoasign_mails = 0;
    public $exc_indv_autoasign = 0;
    public $max_chats = 0;
    public $max_mails = 0;
    public $assign_priority = 0;
    public $chat_min_priority = 0;
    public $chat_max_priority = 0;
    public $dep_id_filter = [];

}

?>