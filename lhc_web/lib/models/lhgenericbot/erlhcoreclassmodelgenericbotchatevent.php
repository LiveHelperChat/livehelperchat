<?php

class erLhcoreClassModelGenericBotChatEvent {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_chat_event';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array (
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'content' => $this->content,
            'ctime' => $this->ctime,
            'counter' => $this->counter
        );

        return $stateArray;
    }

    public function __get($var) {

        switch ($var) {
            case 'content_array':
                $jsonData = json_decode($this->content,true);
                if ($jsonData !== null) {
                    $this->content_array = $jsonData;
                } else {
                    $this->content_array = array();
                }
                return $this->content_array;
                break;

            default:
                break;
        }
    }

    public $id = null;
    public $chat_id = null;
    public $content = '';
    public $ctime = 0;
    public $counter = 0;
}
