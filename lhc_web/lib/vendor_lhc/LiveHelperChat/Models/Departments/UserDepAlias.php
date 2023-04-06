<?php

namespace LiveHelperChat\Models\Departments;

class UserDepAlias {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_userdep_alias';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'dep_id DESC, id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'dep_group_id' => $this->dep_group_id,
            'user_id' => $this->user_id,
            'nick' => $this->nick,
            'filepath' => $this->filepath,
            'filename' => $this->filename,
            'avatar' => $this->avatar
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'user':
                $this->user = \erLhcoreClassModelUser::fetch($this->user_id);
                return $this->user;

                case 'has_photo':
                    return $this->filename != '';

                case 'photo_path':
                    $this->photo_path = ($this->filepath != '' ? '//' . $_SERVER['HTTP_HOST'] . \erLhcoreClassSystem::instance()->wwwDir() : \erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'. $this->filepath . $this->filename;
                    return $this->photo_path;

                case 'file_path_server':
                    return $this->filepath . $this->filename;

            default:
                ;
                break;
        }
    }

    public function beforeRemove()
    {
        $this->removeFile();
    }

    public function removeFile()
    {
        if ($this->filename != '') {
            if ( file_exists($this->filepath . $this->filename) ) {
                unlink($this->filepath . $this->filename);
            }

            if ($this->filepath != '') {
                \erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/userphoto/',str_replace('var/userphoto/','',$this->filepath));
            }

            $this->filepath = '';
            $this->filename = '';
            $this->saveThis();
        }
    }

    public static function getAlias($params) {
        static $cacheAlias = [];

        if ((isset($params['scope']) && in_array($params['scope'],['as_string','typing','msg','canned_replace'])) || (!isset($params['scope']) && $params['chat']->user_id > 0)) {

            if (!isset($params['scope'])) {
                $params['scope'] = 'chat';
            }

            if ($params['scope'] == 'typing') {
                $userId = $params['chat']->operator_typing_user->id;
            } elseif ($params['scope'] == 'canned_replace') {
                if (!is_object($params['user'])) {
                    return;
                }
                $userId = $params['user']->id;
            } elseif ($params['scope'] == 'msg') {
                $userId = $params['msg']->user_id > 0 ? $params['msg']->user_id : (isset($params['user_id']) ? $params['user_id'] : 0);
                if (!($userId > 0)) {
                    return;
                }
            } else {
                $userId = $params['chat']->user_id;
            }

            $cacheKey = $userId . '_dep_' . $params['chat']->id;

            if (isset($cacheAlias[$cacheKey])) {
                $alias = $cacheAlias[$cacheKey];
            } else {
                $db = \ezcDbInstance::get();
                $stmt = $db->prepare('SELECT `dep_group_id` FROM `lh_departament_group_member` WHERE `dep_id` = :dep_id');
                $stmt->bindValue( ':dep_id', $params['chat']->dep_id);
                $stmt->execute();

                $dep_group_ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                $conditions = [
                    'sort' => '`dep_id` DESC',
                    'filter' => [
                        'user_id' => $userId
                    ]
                ];

                if (!empty($dep_group_ids)) {
                    $conditions['customfilter'][] = '(dep_id = ' . (int)$params['chat']->dep_id . ' OR dep_group_id IN (' . implode(',',$dep_group_ids) . '))';
                } else {
                    $conditions['filter']['dep_id'] = $params['chat']->dep_id;
                }

                $alias = self::findOne($conditions);

                $cacheAlias[$cacheKey] = $alias;
            }

            if (is_object($alias)) {

                if ($alias->nick != '') {
                    if ($params['scope'] == 'typing') {
                        $params['chat']->operator_typing_user->name_support = $alias->nick;
                    } elseif ($params['scope'] == 'as_string') {
                        return $alias->nick;
                    } elseif ($params['scope'] == 'msg') {
                        $params['msg']->name_support = $alias->nick;
                    } elseif ($params['scope'] == 'canned_replace') {
                        $params['replace_array']['{operator}'] = $alias->nick;
                    } else {
                        $params['user']->name_support = $alias->nick;
                    }
                }

                if (in_array($params['scope'],['typing','msg','canned_replace','as_string'])) {
                    return; // We are interested only in nick
                }

                $hasAliasPhoto = false;
                if ($alias->has_photo) {
                    $hasAliasPhoto = true;
                    $params['user']->has_photo = true;
                    $params['user']->has_photo_avatar = true;
                    $params['user']->photo_path = $alias->photo_path;
                }

                if ($alias->avatar != '') {
                    $params['user']->avatar = $alias->avatar;

                    if ($hasAliasPhoto == false) {
                        $params['user']->has_photo = false;
                    }
                }
            }
        }
    }

    public $id = null;
    public $dep_id = 0;
    public $dep_group_id = 0;
    public $user_id = 0;
    public $nick = '';
    public $filepath = '';
    public $filename = '';
    public $avatar = '';

}