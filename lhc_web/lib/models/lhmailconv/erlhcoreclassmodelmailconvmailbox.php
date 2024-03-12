<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMailbox
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_mailbox';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'mail' => $this->mail,
            'name' => $this->name,
            'username' => $this->username,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'active' => $this->active,
            'imap' => $this->imap,
            'last_sync_time' => $this->last_sync_time,
            'sync_started' => $this->sync_started,
            'last_sync_log' => $this->last_sync_log,
            'mailbox_sync' => $this->mailbox_sync,
            'sync_status' => $this->sync_status,
            'sync_interval' => $this->sync_interval,
            'signature' => $this->signature,
            'signature_under' => $this->signature_under,
            'import_since' => $this->import_since,
            'delete_mode' => $this->delete_mode,
            'reopen_timeout' => $this->reopen_timeout,
            'uuid_status' => $this->uuid_status,
            'failed' => $this->failed,
            'create_a_copy' => $this->create_a_copy,
            'import_priority' => $this->import_priority,
            'assign_parent_user' => $this->assign_parent_user,
            'mail_smtp' => $this->mail_smtp,
            'name_smtp' => $this->name_smtp,
            'username_smtp' => $this->username_smtp,
            'password_smtp' => $this->password_smtp,
            'no_pswd_smtp' => $this->no_pswd_smtp,
            'user_id' => $this->user_id,
            'dep_id' => $this->dep_id,
            'workflow_options' => $this->workflow_options,
            'auth_method' => $this->auth_method,
            'reopen_reset' => $this->reopen_reset,
            'last_process_time' => $this->last_process_time,
            'delete_on_archive' => $this->delete_on_archive,
            'delete_policy' => $this->delete_policy,
        );
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'mrules':
                if ($this->id > 0) {
                    $mrules = erLhcoreClassModelMailconvMatchRule::getList(['limit' => false, 'customfilter' => ["`mailbox_id` != '' AND JSON_CONTAINS(`mailbox_id`,'" . (int)$this->id . "','$')"]]);
                } else {
                    $mrules = [];
                }
                return $mrules;
            
            case 'mrules_id':
                $mrules_id = array_keys($this->mrules);
                return $mrules_id;

            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->mtime);

            case 'workflow_options_array':
                $this->workflow_options_array = array();
                if ($this->workflow_options != '') {
                    $this->workflow_options_array = json_decode($this->workflow_options, true);
                }
                return $this->workflow_options_array;

            case 'last_sync_log_array':
                $this->last_sync_log_array = array();
                if ($this->last_sync_log != '') {
                    $this->last_sync_log_array = json_decode($this->last_sync_log, true);
                }
                return $this->last_sync_log_array;

            case 'mailbox_sync_array':
                $this->mailbox_sync_array = array();
                if ($this->mailbox_sync != '') {
                    $this->mailbox_sync_array = json_decode($this->mailbox_sync, true);
                }
                return $this->mailbox_sync_array;

            case 'uuid_status_array':
                $this->uuid_status_array = array();
                if ($this->uuid_status != '') {
                    $this->uuid_status_array = json_decode($this->uuid_status, true);
                }
                return $this->uuid_status_array;

            case 'trash_mailbox':
                $this->trash_mailbox = null;
                foreach ($this->mailbox_sync_array as $path) {
                    if (isset($path['sync_deleted']) && $path['sync_deleted'] == true) {
                        $this->trash_mailbox =  preg_replace('/^\{.*\}/','',$path['path']);
                    }
                }
                return $this->trash_mailbox;

            case 'name':
                return $this->mail;

            case 'last_sync_time_ago':
                $this->last_sync_time_ago = erLhcoreClassChat::formatSeconds(time() - $this->last_sync_time);
                return $this->last_sync_time_ago;

            case 'last_process_time_ago':
                $this->last_process_time_ago = erLhcoreClassChat::formatSeconds(time() - $this->last_process_time);
                return $this->last_process_time_ago;

            case 'sync_started_ago':
                $this->sync_started_ago = $this->sync_started > 0 ? erLhcoreClassChat::formatSeconds(time() - $this->sync_started) : '-';
                return $this->sync_started_ago;

            case 'relevant_mailbox_id':
                $this->relevant_mailbox_id = [$this->id];
                $personalMailboxes = erLhcoreClassModelMailconvPersonalMailboxGroup::getList(['customfilter' => ["JSON_EXTRACT(`lhc_mailconv_personal_mailbox_group`.`mails`, '$.{$this->id}') IS NOT NULL"], 'filter' => ['active' => 1]]);
                foreach ($personalMailboxes as $personalMailbox) {
                    $this->relevant_mailbox_id = array_merge($this->relevant_mailbox_id,array_keys($personalMailbox->mails_array));
                }

                $this->relevant_mailbox_id = array_unique($this->relevant_mailbox_id);
                return $this->relevant_mailbox_id;

            default:
                break;
        }
    }

    public function afterSave($params = array())
    {
        if (!is_array($this->mrules_id_update)) {
            return;
        }

        // From which rules we should remove this mailbox
        $rulesRemove = array_diff($this->mrules_id,$this->mrules_id_update);

        // To which rules we should add this mailbox
        $rulesAdd = array_diff($this->mrules_id_update,$this->mrules_id);

        foreach ($rulesRemove as $ruleRemove) {
            $removeRule = erLhcoreClassModelMailconvMatchRule::fetch($ruleRemove);
            $mailBox = $removeRule->mailbox_ids;
            unset($mailBox[array_search($this->id,$mailBox)]);
            $removeRule->mailbox_ids = array_values($mailBox);
            $removeRule->mailbox_id = json_encode($removeRule->mailbox_ids);
            $removeRule->updateThis(['update' => ['mailbox_id']]);
        }

        foreach ($rulesAdd as $ruleAdd) {
            $addRule = erLhcoreClassModelMailconvMatchRule::fetch($ruleAdd);
            $mailBox = $addRule->mailbox_ids;
            $mailBox[] = $this->id;
            $addRule->mailbox_ids = array_values($mailBox);
            $addRule->mailbox_id = json_encode($addRule->mailbox_ids);
            $addRule->updateThis(['update' => ['mailbox_id']]);
        }
    }

    const SYNC_PENDING = 0;
    const SYNC_PROGRESS = 1;

    const DELETE_ALL = 0;
    const DELETE_LOCAL = 1;

    public $id = NULL;
    public $mail = '';
    public $username = '';
    public $password = '';

    public $mail_smtp = '';
    public $name_smtp = '';
    public $username_smtp = '';
    public $password_smtp = '';

    public $host = '';
    public $workflow_options = '';
    public $port = '';
    public $imap = '';
    public $active = 1;
    public $last_sync_time = 0;
    public $last_sync_log = '';
    public $mailbox_sync = '';
    public $signature = '';
    public $sync_status = 0;
    public $sync_started = 0;
    public $sync_interval = 60;
    public $name = '';
    public $signature_under = 0;
    public $import_since = 0;
    public $reopen_timeout = 4;
    public $uuid_status = '';
    public $failed = 0;
    public $create_a_copy = 0;
    public $import_priority = 0;
    public $assign_parent_user = 0;
    public $no_pswd_smtp = 0;
    public $user_id = 0;
    public $dep_id = 0;
    public $reopen_reset = 0;
    public $last_process_time = 0;
    public $mrules_id_update = null;

    public $delete_on_archive = 0;
    public $delete_policy = 0; // 0 - move to trash, 1 - delete on imap server

    public $delete_mode = self::DELETE_ALL;

    const AUTH_NORMAL_PASSWORD = 0;
    const AUTH_OAUTH2 = 1;

    public $auth_method = self::AUTH_NORMAL_PASSWORD;

}

?>