<?php

namespace LiveHelperChat\Models\LHCAbstract;

#[\AllowDynamicProperties]
class ProactiveInvitationOneTime {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_invitation_one_time';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        return array(
            'id'             => $this->id,
            'invitation_id'  => $this->invitation_id,
            'vid_id'         => $this->vid_id,
        );
    }

    public $id             = null;
    public $invitation_id  = null;
    public $vid_id = null;
}
