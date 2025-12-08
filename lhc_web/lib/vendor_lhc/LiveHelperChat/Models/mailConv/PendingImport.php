<?php

namespace LiveHelperChat\Models\mailConv;

#[\AllowDynamicProperties]
class PendingImport
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_pending_import';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,  
            'mailbox_id' => $this->mailbox_id,
            'uid' => $this->uid,
            'status' => $this->status,
            'attempt' => $this->attempt,
            'last_failure' => $this->last_failure,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }

    public function __get($var)
    {
        switch ($var) {
            default:
                break;
        }
    }

    CONST PENDING = 0;
    CONST IGNORE = 1;

    public $id = null;
    public $mailbox_id = 0;
    public $uid = 0;
    public $status = self::PENDING;
    public $attempt = 0;
    public $last_failure = '';
    public $created_at = 0;
    public $updated_at = 0;
}