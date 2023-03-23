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

    public $id = null;
    public $dep_id = 0;
    public $dep_group_id = 0;
    public $user_id = 0;
    public $nick = '';
    public $filepath = '';
    public $filename = '';
    public $avatar = '';

}