<?php

class erLhAbstractModelProactiveChatInvitationEvent {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_invitation_event';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

	public function getState()
	{
		$stateArray = array (
			'id'         	 => $this->id,
			'invitation_id'  => $this->invitation_id,
			'event_id'  	 => $this->event_id,
			'min_number'     => $this->min_number,
			'during_seconds' => $this->during_seconds,			
		);
			
		return $stateArray;
	}

	public function __toString()
	{
		return $this->id;
	}

   	public $id = null;
	public $invitation = '';
	public $event_id = 0;
	public $min_number = 0;
	public $during_seconds = '';
	
	public $hide_add = false;
	public $hide_delete = false;

}

?>