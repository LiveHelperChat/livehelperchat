<?php

class erLhAbstractModelAutoResponder {
    
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_abstract_auto_responder';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';
    
    public static $dbSortOrder = 'DESC';
    
	public function getState()
	{
		$stateArray = array (
			'id'         		=> $this->id,
			'name'  		    => $this->name,
			'siteaccess'  		=> $this->siteaccess,
			'wait_message'		=> $this->wait_message,
			'timeout_message'	=> $this->timeout_message,
			'timeout_message_2'	=> $this->timeout_message_2,
			'timeout_message_3'	=> $this->timeout_message_3,
			'timeout_message_4'	=> $this->timeout_message_4,
			'timeout_message_5'	=> $this->timeout_message_5,
			'wait_timeout'		=> $this->wait_timeout,
			'wait_timeout_2'	=> $this->wait_timeout_2,
			'wait_timeout_3'	=> $this->wait_timeout_3,
			'wait_timeout_4'	=> $this->wait_timeout_4,
			'wait_timeout_5'	=> $this->wait_timeout_5,
		    
			'wait_timeout_reply_1'	  => $this->wait_timeout_reply_1,
			'wait_timeout_reply_2'	  => $this->wait_timeout_reply_2,
			'wait_timeout_reply_3'	  => $this->wait_timeout_reply_3,
			'wait_timeout_reply_4'	  => $this->wait_timeout_reply_4,
			'wait_timeout_reply_5'	  => $this->wait_timeout_reply_5,

			'timeout_hold_message_1'	  => $this->timeout_hold_message_1,
			'timeout_hold_message_2'	  => $this->timeout_hold_message_2,
			'timeout_hold_message_3'	  => $this->timeout_hold_message_3,
			'timeout_hold_message_4'	  => $this->timeout_hold_message_4,
			'timeout_hold_message_5'	  => $this->timeout_hold_message_5,
			'wait_timeout_hold'	          => $this->wait_timeout_hold,

			'wait_timeout_hold_1'	  => $this->wait_timeout_hold_1,
			'wait_timeout_hold_2'	  => $this->wait_timeout_hold_2,
			'wait_timeout_hold_3'	  => $this->wait_timeout_hold_3,
			'wait_timeout_hold_4'	  => $this->wait_timeout_hold_4,
			'wait_timeout_hold_5'	  => $this->wait_timeout_hold_5,
		    
			'timeout_reply_message_1' => $this->timeout_reply_message_1,
			'timeout_reply_message_2' => $this->timeout_reply_message_2,
			'timeout_reply_message_3' => $this->timeout_reply_message_3,
			'timeout_reply_message_4' => $this->timeout_reply_message_4,
			'timeout_reply_message_5' => $this->timeout_reply_message_5,
		    
			'only_proactive'	=> $this->only_proactive,
			'ignore_pa_chat'	=> $this->ignore_pa_chat,
			'dep_id'			=> $this->dep_id,
			'position'			=> $this->position,
			'repeat_number'		=> $this->repeat_number,
			'survey_timeout'	=> $this->survey_timeout,
			'survey_id'		    => $this->survey_id
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}
	
	public function customForm() {
	    return 'autoresponder.tpl.php';
	}
	
   	public function getFields()
   	{
   	    return include ('lib/core/lhabstract/fields/erlhabstractmodelautoresponder.php');
	}

	public function getModuleTranslations()
	{
	    $metaData = array('permission_delete' => array('module' => 'lhchat','function' => 'administrateresponder'),'permission' => array('module' => 'lhchat','function' => 'administrateresponder'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Auto responder'));
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_autoresponder', array('object_meta_data' => & $metaData));
	    
		return $metaData;
	}
	
	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;
	   		
	   	case 'dep':
	   	       if ($this->dep_id > 0) {
	   	           $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
	   	       } else {
	   	           $this->dep = false;
	   	       }
	   		   return $this->dep;
	   		break;
	   		
	   	case 'dep_frontend':
	   	       $this->dep_frontend = $this->dep === false ? '-' : (string)$this->dep;
	   		   return $this->dep_frontend;
	   		break;

	   	case 'wait_timeout_reply_total':
	   	       $this->wait_timeout_reply_total = 0;

               for ($i = 5; $i >= 1; $i--) {
                    if ($this->{'wait_timeout_reply_' . $i} > 0) {
                        $this->wait_timeout_reply_total = $i;
                        break;
                    }
               }

	   		   return $this->wait_timeout_reply_total;
	   		break;

	   	default:
	   		break;
	   }
	}

	public static function processAutoResponder(erLhcoreClassModelChat $chat) {

		$session = erLhcoreClassAbstract::getSession();
		$q = $session->createFindQuery( 'erLhAbstractModelAutoResponder' );
		$q->where( '('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\') AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $chat->dep_id ) ).' OR dep_id = 0) AND only_proactive = 0')
		->orderBy('position ASC')
		->limit( 1 );

		$messagesToUser = $session->find( $q );

		if ( !empty($messagesToUser) ) {
			$message = array_shift($messagesToUser);
			return $message;
		}

		return false;
	}

   	public $id = null;
	public $siteaccess = '';
	public $position = 0;
	public $wait_message = '';
	
	// 1 Message
	public $wait_timeout = 0;
	public $timeout_message = '';
	
	// 2 Message
	public $wait_timeout_2 = 0;
	public $timeout_message_2 = '';
	
	// 3 Message
	public $wait_timeout_3 = 0;
	public $timeout_message_3 = '';
	
	// 4 Message
	public $wait_timeout_4 = 0;
	public $timeout_message_4 = '';
	
	// 5 Message
	public $wait_timeout_5 = 0;
	public $timeout_message_5 = '';

	// On-hold attributes
	public $timeout_hold_message_1 = '';
	public $timeout_hold_message_2 = '';
	public $timeout_hold_message_3 = '';
	public $timeout_hold_message_4 = '';
	public $timeout_hold_message_5 = '';

	public $wait_timeout_hold_1 = 0;
	public $wait_timeout_hold_2 = 0;
	public $wait_timeout_hold_3 = 0;
	public $wait_timeout_hold_4 = 0;
	public $wait_timeout_hold_5 = 0;

	// Not replying attributes
	/* const CHAT_NOT_CLOSED = 0;
	const EXPLICITLY_NOT_CLOSED = 1;
	public $required_chat_status = self::CHAT_NOT_CLOSED; */
	
	// 1 Message
	public $wait_timeout_reply_1 = 0;
	public $timeout_reply_message_1 = '';

	// 2 Message
	public $wait_timeout_reply_2 = 0;
	public $timeout_reply_message_2 = '';

	// 3 Message
	public $wait_timeout_reply_3 = 0;
	public $timeout_reply_message_3 = '';

	// 4 Message
	public $wait_timeout_reply_4 = 0;
	public $timeout_reply_message_4 = '';

	// 5 Message
	public $wait_timeout_reply_5 = 0;
	public $timeout_reply_message_5 = '';

	// Default hold message
	public $wait_timeout_hold = '';

	// Auto responder name
	public $name = '';

	// After hour many seconds in active chat redirect user to survey
	public $survey_timeout = 0;

	// This auto responder applies only to proactive chats
	public $only_proactive = 0;

	// @todo implement
	public $survey_id = 0;

	public $dep_id = 0;
	public $repeat_number = 1;
	
	public $ignore_pa_chat = 0;

	public $hide_add = false;
	public $hide_delete = false;

}

?>