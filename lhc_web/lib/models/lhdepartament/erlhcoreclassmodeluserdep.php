<?php

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
            'active_chats' => $this->active_chats,
            'pending_chats' => $this->pending_chats,
            'inactive_chats' => $this->inactive_chats,
            'hide_online_ts' => $this->hide_online_ts,
            'always_on' => $this->always_on,
        );
    }

    public function __get($var)
    {
        switch ($var) {
            case 'user':
                $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                return $this->user;
                break;

            case 'lastactivity_ago':
                $this->lastactivity_ago = erLhcoreClassChat::getAgoFormat($this->last_activity);
                return $this->lastactivity_ago;
                break;

            case 'offline_since':
                $this->offline_since = erLhcoreClassChat::getAgoFormat($this->hide_online_ts);
                return $this->offline_since;
                break;

            case 'name_support':
                $this->name_support = $this->user->name_support;
                return $this->name_support;
                break;

            case 'name_official':
                $this->name_official = $this->user->name_official;
                return $this->name_official;
                break;

            case 'departments_names':
                $this->departments_names = array();
                $ids = $this->user->departments_ids;

                if ($ids != '') {
                    $parts = explode(',', $ids);
                    sort($parts);

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
                break;

            default:
                break;
        }
    }

    public static function getOnlineOperators($currentUser, $canListOnlineUsersAll = false, $params = array(), $limit = 10, $onlineTimeout = 120)
    {

        $LimitationDepartament = '';
        $userData = $currentUser->getUserData(true);
        $filter = array();

        if ($userData->all_departments == 0 && $canListOnlineUsersAll == false) {
            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());

            if (count($userDepartaments) == 0) return array();

            $index = array_search(-1, $userDepartaments);
            if ($index !== false) {
                unset($userDepartaments[$index]);
            }

            $filter['customfilter'][] = '(dep_id IN (' . implode(',', $userDepartaments) . ') OR user_id = ' . $currentUser->getUserID() . ')';
        };

        $filter['customfilter'][] = '(last_activity > ' . (int)(time() - $onlineTimeout) . ' OR always_on = 1)';

        $filter['limit'] = $limit;

        if (!isset($params['sort'])) {
            $filter['sort'] = 'active_chats DESC, hide_online ASC';
        }

        $filter['group'] = 'user_id';

        $filter = array_merge_recursive($filter, $params);

        $filter['ignore_fields'] = array('id','dep_id','hide_online_ts','hide_online','last_activity','always_on','last_accepted','active_chats','pending_chats','inactive_chats');

        $filter['select_columns'] = '
        max(`id`) as `id`, 
        max(`dep_id`) as `dep_id`,
        max(`hide_online_ts`) as `hide_online_ts`,
        max(`hide_online`) as `hide_online`,
        max(`last_activity`) as `last_activity`, 
        max(`always_on`) as `always_on`, 
        max(`last_accepted`) as `last_accepted`,
        max(`active_chats`) as `active_chats`,
        max(`pending_chats`) as `pending_chats`,
        max(`inactive_chats`) as `inactive_chats`';

        return self::getList($filter);
    }

    public $id = null;
    public $user_id = 0;
    public $dep_id = 0;
    public $hide_online_ts = 0;
    public $hide_online = 0;
    public $last_activity = 0;
    public $last_accepted = 0;
    public $active_chats = 0;
    public $pending_chats = 0;
    public $inactive_chats = 0;
    public $always_on = 0;
}

?>