<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelChatAction {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_action';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'        => $this->id,
            'chat_id'   => $this->chat_id,
            'created_at'   => $this->created_at,
            'action'    => $this->action,
            'body'      => $this->body,
        );
    }

    public function beforeSave($params = array())
    {
        if ($this->created_at == 0) {
            $this->created_at = time();
        }
    }

    public function __get($var){

        switch ($var) {

            case 'chat':
                $this->chat = false;
                if ($this->chat_id > 0){
                    try {
                        $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                    } catch (Exception $e) {
                        $this->chat = new erLhcoreClassModelChat();
                    }
                }
                return $this->chat;

            case 'body_array':
                $this->body_array = null;
                if ($this->body != '') {
                    $jsonData = json_decode($this->body,true);
                    $this->body_array = $jsonData;
                }
                return $this->body_array;

            case 'created_at_front':
                $this->created_at_front = date(erLhcoreClassModule::$dateDateHourFormat,$this->created_at);
                return $this->created_at_front;

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $chat_id = null;
    public $created_at = null;
    public $action = null;
    public $body = null;
}

?>