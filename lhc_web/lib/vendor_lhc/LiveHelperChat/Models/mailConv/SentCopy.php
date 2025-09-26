<?php

namespace LiveHelperChat\Models\mailConv;

class SentCopy {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_sent_copy';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'mailbox_id' => $this->mailbox_id,
            'status' => $this->status,
            'body' => $this->body,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->mailbox_id;
    }

    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;

    public $id = null;
    public $mailbox_id = null;
    public $status = self::STATUS_PENDING;
    public $body = '';
}