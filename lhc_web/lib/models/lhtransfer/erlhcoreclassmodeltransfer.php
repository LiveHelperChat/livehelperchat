<?php

class erLhcoreClassModelTransfer
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_transfer';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassTransfer::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'chat_id' => $this->chat_id,
            'transfer_user_id' => $this->transfer_user_id,
            'from_dep_id' => $this->from_dep_id,
            'transfer_to_user_id' => $this->transfer_to_user_id,
            'ctime' => $this->ctime,
        );
    }

    public function setState(array $properties)
    {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }
    }

    public $id = null;

    public $dep_id = 0;

    public $chat_id = null;

    public $transfer_user_id = 0;

    public $from_dep_id = null;

    public $transfer_to_user_id = 0;

    public $ctime = 0;
}

?>