<?php

namespace LiveHelperChat\Models\LHCAbstract;

class ChatParticipant {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_participant';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id,
            'duration' => $this->duration,
            'time' => $this->time,
            'dep_id' => $this->dep_id,
        );

        return $stateArray;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'duration_front':
                if ($this->duration > 0) {
                    $this->duration_front = \erLhcoreClassChat::formatSeconds($this->duration);
                } else {
                    $this->duration_front = null;
                }
                return $this->duration_front;

            case 'user':
                $this->user = false;
                if ($this->user_id > 0) {
                    try {
                        $this->user = \erLhcoreClassModelUser::fetch($this->user_id,true);
                    } catch (\Exception $e) {
                        $this->user = false;
                    }
                }
                return $this->user;

            case 'user_name':
                return $this->user_name = (string)$this->user;

            case 'plain_user_name':
                $this->plain_user_name = false;

                if ($this->user !== false) {
                    $this->plain_user_name = (string)$this->user->name_support;
                }

                return $this->plain_user_name;

            case 'n_official':
                $this->n_office = false;

                if ($this->user !== false) {
                    $this->n_office = (string)$this->user->name;
                    if ($this->n_office == '') {
                        $this->n_office = $this->plain_user_name;
                    }
                }

                return $this->n_office;


            default:
                ;
                break;
        }
    }

    public $id = null;
    public $chat_id = null;
    public $user_id = null;
    public $duration = null;
    public $dep_id = null;
    public $time = null;
}