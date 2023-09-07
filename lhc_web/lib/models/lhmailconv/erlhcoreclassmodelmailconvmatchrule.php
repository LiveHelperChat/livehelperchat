<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMatchRule
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_match_rule';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'priority_rule ASC, id ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'dep_id' => $this->dep_id,
            'active' => $this->active,
            'conditions' => $this->conditions,
            'mailbox_id' => $this->mailbox_id,
            'subject_contains' => $this->subject_contains,
            'from_name' => $this->from_name,
            'from_mail' => $this->from_mail,
            'priority_rule' => $this->priority_rule,
            'priority' => $this->priority,
            'options' => $this->options,
        );
    }

    public function __toString()
    {
        return $this->display_name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return '';

            case 'display_name':
                $mailboxIntroNames = '';
                if (!empty($this->mailbox_id)) {
                    $mailboxids = json_decode($this->mailbox_id, true);
                    if (!empty($mailboxids)) {
                        $mailboxIntroNames = implode(', ',erLhcoreClassModelMailconvMailbox::getList(['limit' => 5, 'filterin' => ['id' => $mailboxids]]));
                    }
                }
                $this->display_name = '[' . $this->id . '] '. ($this->name != '' ? ' ' . $this->name . ' | ' : '') . ($this->dep_id > 0 ? $this->department . ' | ' : '') . $mailboxIntroNames;
                return $this->display_name;

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
                if ($this->from_mail != '') {
                    $this->from_mail_array = explode(',',str_replace(["\n","\r"],"",$this->from_mail));
                } else {
                    $this->from_mail_array = [];
                }
                return $this->from_mail_array;

            case 'conditions_array':
                $conditions_array = json_decode($this->conditions,true);
                if ($conditions_array === null) {
                    $conditions_array = [];
                }
                $this->conditions_array = $conditions_array;
                return $this->conditions_array;

            case 'options_array':
                $options_array = json_decode($this->options,true);
                if ($options_array === null) {
                    $options_array = [];
                }
                $this->options_array = $options_array;
                return $this->options_array;

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
    public $options = '';
    public $name = '';
    public $priority_rule = 0;
}

?>