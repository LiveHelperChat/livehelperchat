<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelCannedMsgUse
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_canned_msg_use';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'canned_id' => $this->canned_id,
            'ctime' => $this->ctime,
            'user_id' => $this->user_id,
            'chat_id' => $this->chat_id
        );
    }

    public static function logUse($params) {
        $item = new self();
        $item->setState($params);
        $item->saveThis();
    }

    public function __get($var)
    {
        switch ($var) {

            default:
                break;
        }
    }

    public $id = null;
    public $canned_id = null;
    public $ctime = null;
    public $user_id = null;
    public $chat_id = null;
}

?>