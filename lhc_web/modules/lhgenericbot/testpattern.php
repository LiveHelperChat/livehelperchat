<?php

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['id']);

$response = "";

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {

    if (isset($_POST['extract_action'])) {
        $patterns = [];
        foreach ($chat->getState() as $stateKey => $stateAttr) {
            $patterns[] = '{args.chat.' . $stateKey .'} = ' . $stateAttr;
        }

        $subAttributes = [
            'online_user' => ['online_attr_system_array','online_attr_array'],
            'department' => ['bot_configuration_array'],
            'iwh' => ['conditions_array']
        ];

        foreach (['subject_ids','subject_ids_list','department','chat_variables_array','user','online_user',
                  'wait_time_pending','incoming_chat','iwh','bot','user_status_front','chat_dynamic_array','aalert','aicons','msg_v','additional_data_array','user_tz_identifier_time',
            'unread_time','screenshot','number_in_queue','department_name','department_role','auto_responder','n_off_full','n_official','hum','plain_user_name','user_name','chat_duration_front','pnd_rsp',
            'wait_time_pending','last_user_msg_time_front','start_last_action_front','wait_time_front','last_msg_time_front','last_msg_time','wait_time_seconds','is_user_typing','can_edit_chat','is_operator_typing',
            'user_closed_ts_front','cls_time_front','pnd_time_front','time_created_front','last_msg'
                     ] as $dynamicAttr) {
            if (is_object($chat->{$dynamicAttr})) {
                foreach ($chat->{$dynamicAttr}->getState() as $stateKey => $stateAttr) {
                    $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $stateKey .'} = ' . ((is_array($stateAttr) || is_object($stateAttr)) ? json_encode($stateAttr) : $stateAttr);
                }
                if (isset($subAttributes[$dynamicAttr])){
                    foreach ($subAttributes[$dynamicAttr] as $subStateAttr) {
                        foreach ($chat->{$dynamicAttr}->{$subStateAttr} as $stateSubKey => $subStateAttrVal) {
                            $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $subStateAttr . '.' . $stateSubKey . '} = ' . ((is_array($subStateAttrVal) || is_object($subStateAttrVal)) ? json_encode($subStateAttrVal) : $subStateAttrVal);
                        }
                    }
                }
            } elseif (is_array($chat->{$dynamicAttr})) {
                foreach ($chat->{$dynamicAttr} as $stateKey => $stateAttr) {
                    $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $stateKey .'} = ' . ((is_array($stateAttr) || is_object($stateAttr)) ? json_encode($stateAttr) : $stateAttr);
                }
            } elseif (is_string($chat->{$dynamicAttr}) || is_numeric($chat->{$dynamicAttr}) || is_bool($chat->{$dynamicAttr})) {
                $patterns[] = '{args.chat.' . $dynamicAttr .'} = ' . ((is_array($chat->{$dynamicAttr}) || is_object($chat->{$dynamicAttr})) ? json_encode($chat->{$dynamicAttr}) : $chat->{$dynamicAttr});
            }
        }

        $response = implode("\n",$patterns);

    } else {
        $response = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('as_json' => true, 'chat' => $chat, 'args' => ['chat' => $chat]));
        if (strpos($response,'{') === 0) {
            $response = json_encode(json_decode($response,true), JSON_PRETTY_PRINT);
        }
    }
} else {
    $response = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Chat does not exists or you do not have permission to access it!');
}

echo nl2br(htmlspecialchars($response));
exit;