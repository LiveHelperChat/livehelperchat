<?php

namespace LiveHelperChat\Models\mailConv;

class OAuthMS {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_oauth_ms';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'oauth_uid' => $this->oauth_uid,
            'name' => $this->name,
            'surname' => $this->surname,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'mailbox_id' => $this->mailbox_id,
            'txtSessionKey' => $this->txtSessionKey,
            'txtCodeVerifier' => $this->txtCodeVerifier,
            'dtExpires' => $this->dtExpires,
            'txtRefreshToken' => $this->txtRefreshToken,
            'txtToken' => $this->txtToken,
            'txtIDToken' => $this->txtIDToken,
            'completed' => $this->completed,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $oauth_uid = '';
    public $mailbox_id = 0;
    public $name = '';
    public $surname = '';
    public $display_name = '';
    public $txtSessionKey = '';
    public $txtCodeVerifier = '';
    public $dtExpires = '';
    public $txtRefreshToken = '';
    public $txtToken = '';
    public $txtIDToken = '';
    public $completed = 0;

    public $email = '';
}