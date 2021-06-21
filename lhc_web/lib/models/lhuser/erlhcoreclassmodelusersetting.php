<?php

class erLhcoreClassModelUserSetting
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_users_setting';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'identifier' => $this->identifier,
            'value' => $this->value
        );
    }

    public function __toString()
    {
        return $this->value;
    }

    public static function setSetting($identifier, $value, $user_id = false)
    {
        if ($user_id == false) {
            $currentUser = erLhcoreClassUser::instance();
            if ($currentUser->isLogged()) {
                $user_id = $currentUser->getUserID();
            }
        }

        if ($user_id !== false) {
            $list = self::getList(array('filter' => array('user_id' => $user_id, 'identifier' => $identifier)));

            if (count($list) > 0) {
                $item = array_shift($list);
            } else {
                $item = new erLhcoreClassModelUserSetting();
                $item->user_id = $user_id;
                $item->identifier = $identifier;
            }

            $item->value = $value;

            $item->saveThis();

            CSCacheAPC::getMem()->store('settings_user_id_' . $user_id . '_' . $identifier, $value);
            CSCacheAPC::getMem()->setSession('settings_user_id_' . $user_id . '_' . $identifier, $value, true);

        } else {
            CSCacheAPC::getMem()->setSession('anonymous_' . $identifier, $value);
        }
    }

    public static function getSetting($identifier, $default_value, $user_id = false, $noSession = false)
    {
        if ($user_id == false) {
            $currentUser = erLhcoreClassUser::instance();
            if ($currentUser->isLogged()) {
                $user_id = $currentUser->getUserID();
            }
        }

        if ($user_id !== false) {

            $value = CSCacheAPC::getMem()->getSession('settings_user_id_' . $user_id . '_' . $identifier, true);

            if ($value === false && ($value = CSCacheAPC::getMem()->restore('settings_user_id_' . $user_id . '_' . $identifier)) === false) {
                $value = $default_value;
                $list = self::getList(array('filter' => array('user_id' => $user_id, 'identifier' => $identifier)));

                if (count($list) > 0) {
                    $item = array_shift($list);
                    $value = $item->value;
                } else {
                    $item = new erLhcoreClassModelUserSetting();
                    $item->value = $default_value;
                    $item->user_id = $user_id;
                    $item->identifier = $identifier;
                    $item->saveThis();
                }

                CSCacheAPC::getMem()->store('settings_user_id_' . $user_id . '_' . $identifier, $value);
                CSCacheAPC::getMem()->setSession('settings_user_id_' . $user_id . '_' . $identifier, $value, true);
            }
        } else {
            $value = $default_value;

            if ($noSession === false && ($value = CSCacheAPC::getMem()->getSession('anonymous_' . $identifier)) === false) {
                $value = $default_value;
                CSCacheAPC::getMem()->setSession('anonymous_' . $identifier, $value);
            }
        }

        return $value;
    }

    public $id = null;
    public $user_id = null;
    public $identifier = '';
    public $value = '';
}

?>