<?php
#[\AllowDynamicProperties]
class erLhAbstractModelProactiveChatInvitationDep {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_invitation_dep';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array (
            'id'         	 => $this->id,
            'invitation_id'  => $this->invitation_id,
            'dep_id'  	 => $this->dep_id,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->id;
    }

    public $id = null;
    public $invitation_id = '';
    public $dep_id = 0;

    public $hide_add = false;
    public $hide_delete = false;

}

?>