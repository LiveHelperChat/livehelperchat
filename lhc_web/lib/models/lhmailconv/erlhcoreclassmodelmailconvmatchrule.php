<?php

class erLhcoreClassModelMailconvMatchRule
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_match_rule';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'priority_rule ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'active' => $this->active,
            'conditions' => $this->conditions,
            'mailbox_id' => $this->mailbox_id,
            'subject_contains' => $this->subject_contains,
            'from_name' => $this->from_name,
            'from_mail' => $this->from_mail,
            'priority_rule' => $this->priority_rule,
            'priority' => $this->priority,
        );
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return '';

            case 'department':
                $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->department;

            case 'mailbox_ids':
                if ($this->mailbox_id != '') {
                    $this->mailbox_ids = json_decode($this->mailbox_id, true);
                } else {
                    $this->mailbox_ids = [];
                }
                return $this->mailbox_ids;

            case 'mailbox_object_ids':
                $mailboxids = $this->mailbox_ids;
                if (!empty($mailboxids)) {
                    $this->mailbox_object_ids = erLhcoreClassModelMailconvMailbox::getList(['filterin' => ['id' => $mailboxids]]);
                } else {
                    $this->mailbox_object_ids = [];
                }
                return $this->mailbox_object_ids;

            case 'from_mail_array':
                $this->from_mail_array = explode(',',str_replace(["\n","\r"],"",$this->from_mail));
                return $this->from_mail_array;
                break;


            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $dep_id = '';
    public $active = 1;
    public $conditions = '';
    public $mailbox_id = '';
    public $subject_contains = '';
    public $from_name = '';
    public $from_mail = '';
    public $priority = 0;
    public $priority_rule = 0;
}

?>