<?php

class erLhAbstractModelProactiveChatCampaignConversion
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_campaign_conv';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id'                => $this->id,
            'device_type'       => $this->device_type,
            'invitation_type'   => $this->invitation_type,
            'invitation_status' => $this->invitation_status,
            'chat_id'           => $this->chat_id,
            'campaign_id'       => $this->campaign_id,
            'invitation_id'     => $this->invitation_id,
            'department_id'     => $this->department_id,
            'ctime'             => $this->ctime,
            'con_time'          => $this->con_time,
            'vid_id'            => $this->vid_id
        );

        return $stateArray;
    }

    public function __get($var)
    {
        switch ($var) {

            default:
                break;
        }
    }

    const INV_SEND = 0;
    const INV_SHOWN = 1;
    const INV_SEEN = 2;
    const INV_CHAT_STARTED = 3;

    public $id = null;
    public $device_type = 0;
    public $invitation_type = 0;
    public $invitation_status = self::INV_SEND;
    public $chat_id = 0;
    public $campaign_id = 0;
    public $invitation_id = 0;
    public $department_id = 0;
    public $ctime = 0;
    public $con_time = 0;
    public $vid_id = 0;
}

?>