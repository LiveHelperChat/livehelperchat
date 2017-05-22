<?php

/**
 * @desc Responsible for events tracking.
 * 
 * */
class erLhcoreClassChatEvent
{
    /**
     * @desc Logs provided events
     * 
     * @param array $events
     * 
     * @param erLhcoreClassModelChatOnlineUser $vid
     */
    public static function logEvents($events, erLhcoreClassModelChatOnlineUser $vid)
    {
        $ids = array();
        
        foreach ($events as $event) {
            $ids[] = $event['id'];
        }
        
        if (empty($ids)) {
            return;
        }
        
        $variables = erLhAbstractModelProactiveChatVariables::getList(array(
            'filterin' => array(
                'identifier' => $ids
            )
        ));
        
        if (empty($variables)) {
            return;
        }
        
        $variablesKeyed = array();
        foreach ($variables as $variable) {
            $variablesKeyed[$variable->identifier] = $variable;
        }
        
        $eventsTouched = array();
        
        foreach ($events as $event) {
            if (isset($variablesKeyed[$event['id']])) {
                
                $filter = array(
                    'filter' => array (
                        'vid_id' => $vid->id,
                        'ev_id' => $variablesKeyed[$event['id']]->id
                    )
                );
                
                if ($variablesKeyed[$event['id']]->store_timeout > 0) {
                    $filter['filtergt']['ts'] = time() - $variablesKeyed[$event['id']]->store_timeout;
                }
                
                if ($variablesKeyed[$event['id']]->filter_val == 1 && isset($event['val']) && $event['val'] == '') {
                    $filter['filter']['val'] = $event['val'];
                }
                
                $eventObj = erLhAbstractModelProactiveChatEvent::findOne($filter);

                if ($eventObj === false) {
                    $eventObj = new erLhAbstractModelProactiveChatEvent();
                }

                $eventObj->ev_id = $variablesKeyed[$event['id']]->id;
                $eventObj->vid_id = $vid->id;
                $eventObj->ts = time();
                $eventObj->val = isset($event['val']) ? $event['val'] : '';
                $eventObj->saveThis(); 

                $eventsTouched[] = $eventObj;
            }
        }
        
        if (!empty($eventsTouched)) {
            self::processInvitation($vid, $eventsTouched);
        }
    }
    
    /**
     * @desc 
     * 1. we find all proactive invitation which has any of logged variables
     * 2. Then we go foreach invitation and search does it meets our requirement.
     * 
     * @param erLhcoreClassModelChatOnlineUser $vid
     * 
     * @param array of erLhAbstractModelProactiveChatEvent
     * 
     */
    public static function processInvitation(erLhcoreClassModelChatOnlineUser $vid, $events)
    {
        $idEv = array();
        
        foreach ($events as $evn) {
            $idEv[] = $evn->ev_id;
        }
        
        // Select related invitations
        $sql = "SELECT invitation_id FROM lh_abstract_proactive_chat_invitation_event WHERE event_id IN (" . implode(',', $idEv) . ') GROUP BY invitation_id';
        
        $db = ezcDbInstance::get();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $eventsToMatch = erLhAbstractModelProactiveChatInvitationEvent::getList(array('filterin' => array('invitation_id' => $rows)));
        
        $invitationsConditions = array();
        
        foreach ($eventsToMatch as $event) {
            $invitationsConditions[$event->invitation_id][] = $event;
        }

        $invitationsValid = array();
        
        foreach ($invitationsConditions as $invitationId => $conditionGroup) {
            
            $conditionsMet = true;
             
            foreach ($conditionGroup as $condition) {
                $sqlConditions = array();
                $sqlConditions['filter']['vid_id'] = $vid->id;
                
                if ($condition->during_seconds > 0) {
                    $sqlConditions['filtergt']['ts'] = time() - $condition->during_seconds;
                }

                $sqlConditions['filter']['ev_id'] = $condition->event_id;

                $foundTimes = erLhAbstractModelProactiveChatEvent::getCount($sqlConditions);
                                
                // No need to process any futher if atleast one condition is not met
                if ($foundTimes == 0 || ($foundTimes <= $condition->min_number)) {                                        
                    $conditionsMet = false;
                    break;
                }                
            }
            
            if ($conditionsMet == true) {
                $invitationsValid[] = $invitationId;
            }
        }
        
        if (!empty($invitationsValid)) {

            $enabledInvitations = true;
            
            if ($vid->reopen_chat == 1 && ($chat = $vid->chat) !== false && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN) {
                $enabledInvitations = false;
            }
                                    
            // If there is no assigned default proactive invitations find dynamic one triggers
            if ($enabledInvitations == true && $vid->operator_message == '' && $vid->message_seen == 0) {
                erLhAbstractModelProactiveChatInvitation::processProActiveInvitation($vid, array('ignore_event' => true, 'invitation_id' => $invitationsValid));
                $vid->saveThis();
            }
        }
    }

    /**
     * @desc If page view is executed and invitation is found and this invitations has events to be triggered. This function checks does required events are valid for specific user.
     * 
     * @param erLhcoreClassModelChatOnlineUser $vid
     * 
     * @param erLhAbstractModelProactiveChatInvitation $invitation
     * 
     * @return boolean
     */
    public static function isConditionsSatisfied(erLhcoreClassModelChatOnlineUser $vid, erLhAbstractModelProactiveChatInvitation $invitation)
    {
        $conditionGroup = erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $invitation->id)));

        foreach ($conditionGroup as $condition) {
            $sqlConditions = array();
            $sqlConditions['filter']['vid_id'] = $vid->id;
        
            if ($condition->during_seconds > 0) {
                $sqlConditions['filtergt']['ts'] = time() - $condition->during_seconds;
            }
        
            $sqlConditions['filter']['ev_id'] = $condition->event_id;
        
            $foundTimes = erLhAbstractModelProactiveChatEvent::getCount($sqlConditions);
        
            // No need to process any futher if atleast one condition is not met
            if ($foundTimes == 0 || ($foundTimes <= $condition->min_number)) {
                return false;
                break;
            }
        }

        return true;
        
    }
    
    /**
     * @desc Validates proactive invitation editing and creates event triggers
     * 
     * @param array $params
     */
    public static function validateProactive($params)
    {
        $definition = array(           
            // Custom fields from back office
            'event_variable' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'min_number' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'during_seconds' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'event_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();
        
        $params['obj']->events = array();
        
        if ( $form->hasValidData( 'event_id' ) && !empty($form->event_id)) {
            $customFields = array();
            foreach ($form->event_id as $key => $idEvent) {
                if (is_numeric($idEvent)) {
                    $invitationEvent = erLhAbstractModelProactiveChatInvitationEvent::fetch($idEvent);
                } else {
                    $invitationEvent = new erLhAbstractModelProactiveChatInvitationEvent();
                }
                
                $invitationEvent->invitation_id = $params['obj']->id;
                $invitationEvent->event_id = $form->event_variable[$key];
                $invitationEvent->during_seconds = $form->during_seconds[$key];
                $invitationEvent->min_number = $form->min_number[$key];
                $invitationEvent->id_event = $idEvent;
                           
                $params['obj']->events[] = $invitationEvent;
            }
        }      
    }
    
    public static function getEventJson($events)
    {
        $eventsJson = array();
        
        foreach ($events as $event) {
            $eventsJson[] = array (
                'event_type' => $event->event_id,
                'min_number' => $event->min_number,
                'during_seconds' => $event->during_seconds,
                'id' => $event->id == null ? $event->id_event : $event->id
            );
        }
        
        return json_encode($eventsJson);
    }
}

?>