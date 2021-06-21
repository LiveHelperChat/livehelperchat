<?php

class erLhcoreClassModelChatIncoming {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_incoming';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'                => $this->id,
            'chat_external_id'  => $this->chat_external_id,
            'chat_id'           => $this->chat_id,
            'incoming_id'       => $this->incoming_id,
            'utime'             => $this->utime,
            'payload'           => $this->payload
        );
    }

    public function __get($var) {

        switch ($var) {

            case 'chat':
                $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                return $this->chat;

            case 'incoming':
                $this->incoming = erLhcoreClassModelChatIncomingWebhook::fetch($this->incoming_id);
                return $this->incoming;

            case 'payload_array':
                $this->payload_array = array();
                if ($this->payload != '') {
                    $jsonData = json_decode($this->payload,true);
                    if ($jsonData !== null) {
                        $this->payload_array = $jsonData;
                    } else {
                        $this->payload_array = array();
                    }
                }
                return $this->payload_array;


            default:
                break;
        }
    }

    public $id = null;

    // Our main chat id
    public $chat_id = 0;

    // External chat id
    public $chat_external_id = '';

    // Which webhook was used
    public $incoming_id = 0;

    // Initial payload because of which we have started a chat
    public $payload = '';

    // Last update happened
    public $utime = 0;
}

?>