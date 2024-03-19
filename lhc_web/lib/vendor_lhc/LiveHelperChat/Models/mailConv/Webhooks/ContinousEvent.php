<?php
namespace LiveHelperChat\Models\mailConv\Archive;

class ContinousEvent
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_mail_continous_event';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'message_id' => $this->message_id,
            'status' => $this->status,
            'webhook_id' => $this->webhook_id
        );
    }

    public function removeThis()
    {

    }

    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_DONE = 2;

    public $message_id= null;
    public $status = 0;
    public $webhook_id = 0;
}

?>