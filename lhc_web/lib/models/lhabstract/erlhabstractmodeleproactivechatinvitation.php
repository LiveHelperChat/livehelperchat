<?php

class erLhAbstractModelProactiveChatInvitation {

    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_abstract_proactive_chat_invitation';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'siteaccess'  	=> $this->siteaccess,
			'time_on_site'  => $this->time_on_site,
			'referrer' 		=> $this->referrer,
			'pageviews' 	=> $this->pageviews,
			'message' 			=> $this->message,
			'autoresponder_id' 	=> $this->autoresponder_id,
			'message_returning' => $this->message_returning,
			'message_returning_nick' => $this->message_returning_nick,
			'identifier' 	=> $this->identifier,
			'dep_id' 		=> $this->dep_id,
			'executed_times'=> $this->executed_times,
			'position'		=> $this->position,
			'operator_name'	=> $this->operator_name,
			'requires_email'		=> $this->requires_email,
			'requires_username'		=> $this->requires_username,
			'show_random_operator'	=> $this->show_random_operator,
			'hide_after_ntimes'	    => $this->hide_after_ntimes,
			'operator_ids'	    => $this->operator_ids,
			'requires_phone'	=> $this->requires_phone,
			'tag' => $this->tag,
			'dynamic_invitation' => $this->dynamic_invitation,
			'event_invitation' => $this->event_invitation,
			'iddle_for' => $this->iddle_for,
			'event_type' => $this->event_type,
			'show_on_mobile' => $this->show_on_mobile
		);
			
		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}
	
	public function checkPermission(){
		
		$currentUser = erLhcoreClassUser::instance();
		
		/**
		 * Append user departments filter
		 * */
		$departmentParams = array();
		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
		if ($userDepartments !== true) {
			if (!in_array($this->dep_id, $userDepartments)) {
				return false;
			}
		}
	}
	
	public static function getFilter(){
		
		$currentUser = erLhcoreClassUser::instance();
		$departmentParams = array();
		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
		if ($userDepartments !== true){
			$departmentParams['filterin']['dep_id'] = $userDepartments;
		}
		
		return $departmentParams;
	}

	public function getFields()
   	{
   	    $currentUser = erLhcoreClassUser::instance();
   		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
   		
   		return include('lib/core/lhabstract/fields/erlhabstractmodeleproactivechatinvitation.php');
	}

	public static function getEventTypes()
	{
	    $items = array();
	    
	    $item = new stdClass();
	    $item->id = 1;
	    $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Mouse leaves a browser window');
	    
	    $items[] = $item;
	    
	    $item = new stdClass();
	    $item->id = 2;
	    $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Visitor idle N seconds on site');
	     
	    $items[] = $item;
	    
	    return $items;
	}

	public function getModuleTranslations()
	{
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    $metaData = array('permission_delete' => array('module' => 'lhchat','function' => 'administrateinvitations'),'permission' => array('module' => 'lhchat','function' => 'administrateinvitations'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Pro active chat invitations'));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_proactive', array('object_meta_data' => & $metaData));	
	    
		return $metaData;
	}

	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;
	   		
	   	case 'events':
	   	       $this->events = erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id)));
	   	       return $this->events;
	   	    break;

	   	case 'autoresponder':
	   	       if ($this->autoresponder_id > 0) {
	   	            $this->autoresponder = erLhAbstractModelAutoResponder::fetch($this->autoresponder_id);
	   	       } else {
                   $this->autoresponder = false;
               }
	   	       return $this->autoresponder;
	   	    break;
	   	    
	   	default:
	   		break;
	   }
	}

	public static function getHost($url) {
		$url = parse_url($url);
		if (isset($url['host'])) {
			return str_replace('www.','',$url['host']);
		}
		
		return '';
	}
	
	public static function processProActiveInvitationDynamic(erLhcoreClassModelChatOnlineUser & $item, $params = array())
	{
	    $referrer = self::getHost($item->referrer);
	    
	    $session = erLhcoreClassAbstract::getSession();
	    $appendTag = '';
	    
	    $q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );
	    
	    if (isset($params['tag']) && $params['tag'] != '') {
	        $appendTag = 'AND ('.$q->expr->eq( 'tag', $q->bindValue( $params['tag'] ) ).' OR tag = \'\')';
	    } else {
	        $appendTag = 'AND (tag = \'\')';
	    }
	    
	    $q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
				AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $item->dep_id ) ).' OR dep_id = 0)
	            AND `dynamic_invitation` = 1
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
	    )
	    ->orderBy('position ASC')
	    ->limit( 10 );

	    $messagesToUser = $session->find( $q );
	    
	    return $messagesToUser;
	}
	
	public static function setInvitation(erLhcoreClassModelChatOnlineUser & $item, $invitationId) {
	    
	    $message = self::fetch($invitationId);
	    
	    if ($item->total_visits == 1 || $message->message_returning == '') {
	        $item->operator_message = $message->message;
	    } else {
	        if ($item->chat !== false && $item->chat->nick != '') {
	            $nick = $item->chat->nick;
	        } elseif ($message->message_returning_nick != '') {
	            $nick = $message->message_returning_nick;
	        } else {
	            $nick = '';
	        }
	    
	        $item->operator_message = str_replace('{nick}', $nick, $message->message_returning);
	    }

	    $item->operator_user_proactive = $message->operator_name;
	    $item->invitation_id = $message->id;
	    $item->invitation_seen_count = 0;
	    $item->requires_email = $message->requires_email;
	    $item->requires_username = $message->requires_username;
	    $item->requires_phone = $message->requires_phone;
	    $item->invitation_count++;
	    $item->store_chat = true;
	    $item->invitation_assigned = true;
	    $item->last_visit = time();
	    
	    if ($message->show_random_operator == 1) {
	        $item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => explode(',',trim($message->operator_ids))));
	    }
	    
	    $message->executed_times += 1;
	    $message->updateThis();
	    	
	    $item->saveThis();
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('message' => & $message, 'ou' => & $item));
	}
	
	public static function processProActiveInvitation(erLhcoreClassModelChatOnlineUser & $item, $params = array()) {

		$referrer = self::getHost($item->referrer);

		$session = erLhcoreClassAbstract::getSession();			
		$appendTag = '';
		
		$q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );
		
		if (isset($params['tag']) && $params['tag'] != '') {
		    $appendTag = 'AND ('.$q->expr->eq( 'tag', $q->bindValue( $params['tag'] ) ).' OR tag = \'\')';
		} else {
		    $appendTag = 'AND (tag = \'\')';
		}
		
		$appendInvitationsId = '';
		if ( isset($params['invitation_id']) && !empty($params['invitation_id']) ) {
		    $appendInvitationsId = 'AND id IN ('.implode(',', $params['invitation_id']).')';
		}

		$q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
		        AND `dynamic_invitation` = 0
		        ' . $appendInvitationsId . '
				AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $item->dep_id ) ).' OR dep_id = 0)
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
		)
		->orderBy('position ASC')
		->limit( 1 );
		
		$messagesToUser = $session->find( $q );
		
		if ( !empty($messagesToUser) ) {
			$message = array_shift($messagesToUser);
			
			if ($message->event_invitation == 1 && (!isset($params['ignore_event']) || $params['ignore_event'] == 0)) {
			    
			    // Event conditions does not satisfied
			    if (erLhcoreClassChatEvent::isConditionsSatisfied($item, $message) === false) {
			        return;
			    }
			}
			
			// Use default message if first time visit or returning message is empty
			if ($item->total_visits == 1 || $message->message_returning == '') {			
				$item->operator_message = $message->message;
			} else {				
				if ($item->chat !== false && $item->chat->nick != '') {
					$nick = $item->chat->nick;
				} elseif ($message->message_returning_nick != '') {
					$nick = $message->message_returning_nick;
				} else {
					$nick = '';
				}
				
				$item->operator_message = str_replace('{nick}', $nick, $message->message_returning);				
			}
			
			$item->operator_user_proactive = $message->operator_name;
			$item->invitation_id = $message->id;
			$item->invitation_seen_count = 0;
			$item->requires_email = $message->requires_email;
			$item->requires_username = $message->requires_username;
			$item->requires_phone = $message->requires_phone;
			$item->invitation_count++;
			$item->store_chat = true;
			$item->invitation_assigned = true;
			$item->last_visit = time();
			$item->show_on_mobile = $message->show_on_mobile;

			if ($message->show_random_operator == 1) {
				$item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => explode(',',trim($message->operator_ids))));				
			}

			$message->executed_times += 1;
			$message->updateThis();
			
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('message' => & $message, 'ou' => & $item));
		}
	}
	
	public function customForm(){
	    return 'proactive_invitation.tpl.php';
	}
	
	public function dependFooterJs(){
	    return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.lhc.events.js').'"></script>';
	}
	
	public function validateInput($params)
	{
	    $params['obj'] = & $this;
	    erLhcoreClassChatEvent::validateProactive($params);
	}
	
	public function afterUpdate()
	{
	    $ids = array();
	    
	    // Save events and collect id's
	    foreach ($this->events as $event) {
	        $event->saveThis();
	        $ids[] = $event->id;
	    }
	    
	    // Remove old, non-existing events
	    foreach (erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id))) as $oldEvent) {
	        if (!in_array($oldEvent->id, $ids)) {
	            $oldEvent->removeThis();
	        }
	    }	

	    if (empty($ids) && $this->event_invitation == 1) {
	        $this->event_invitation = 0;
	        $this->saveThis();
	    } elseif (!empty($ids) && $this->event_invitation == 0) {
	        $this->event_invitation = 1;
	        $this->saveThis();
	    }
	}

	public function afterRemove()
	{
	    foreach (erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id))) as $oldEvent) {
            $oldEvent->removeThis();
	    }
	}
	
   	public $id = null;
	public $siteaccess = '';
	public $time_on_site = 0;
	public $pageviews = 0;
	public $message = '';
	public $message_returning = '';
	public $message_returning_nick = '';
	public $position = 0;
	public $requires_email = 0;
	public $requires_username = 0;
	public $requires_phone = 0;
	public $name = '';
	public $identifier = '';
	public $executed_times = 0;
	public $operator_name = '';
	public $show_random_operator = 0;
	public $hide_after_ntimes = 0;
	public $dep_id = 0;
	public $referrer = '';
	public $operator_ids = '';
	public $tag = '';
	public $dynamic_invitation = 0;
	public $event_invitation = 0;
	public $iddle_for = 0;
	public $event_type = 0;
	public $autoresponder_id = 0;
	public $show_on_mobile = 0;

	public $hide_add = false;
	public $hide_delete = false;

}

?>