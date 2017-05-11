<?php

/**
 * Responsible for events tracking
 * 
 * */
class erLhcoreClassChatEvent
{

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
                
                if ($variablesKeyed[$event['id']]->is_persistent == 0) {
                    if ($variablesKeyed[$event['id']]->store_timeout > 0) {
                        $filter['filtergt']['ts'] = time() - $variablesKeyed[$event['id']]->store_timeout;
                    }
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
    }
    
    /**
     * @desc 
     * 1. we find all proactive invitation which has any of logged variables
     * 2. Then we go foreach invitation and search does it meets our requirement.
     * 
     */
    public function processInvitation(erLhcoreClassModelChatOnlineUser $vid, $events)
    {
        /* event_trigger_invitation
        proactive_invitation_id
        id event_id min_number since_last */
    }

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