<?php

if ($Params['user_parameters']['type'] == 'restapi') {

    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/usecasesrestapi.tpl.php');

    $restAPI = erLhcoreClassModelGenericBotRestAPI::fetch((int)$Params['user_parameters']['id']);
    
    if ($restAPI instanceof erLhcoreClassModelGenericBotRestAPI) {
        $db = ezcDbInstance::get();
        
        // Find triggers that use this REST API
        $stmt = $db->prepare('SELECT id, name, bot_id FROM lh_generic_bot_trigger WHERE actions LIKE :rest_api_id OR actions LIKE :rest_api_id_2');
        $stmt->bindValue(':rest_api_id', '%"rest_api":' . $restAPI->id . '%', PDO::PARAM_STR);
        $stmt->bindValue(':rest_api_id_2', '%"rest_api":"' . $restAPI->id . '"%', PDO::PARAM_STR);
        $stmt->execute();
        $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $items = [];
        
        foreach ($triggers as $trigger) {
            // Parse trigger actions to find which specific methods are being used
            $triggerObj = erLhcoreClassModelGenericBotTrigger::fetch($trigger['id']);
            if ($triggerObj instanceof erLhcoreClassModelGenericBotTrigger) {
                $actions = $triggerObj->actions_front;
                $methodsUsed = [];
                
                foreach ($actions as $action) {
                    if (isset($action['content']['rest_api']) && 
                        $action['content']['rest_api'] == $restAPI->id &&
                        isset($action['content']['rest_api_method'])) {
                        $methodId = $action['content']['rest_api_method'];
                        
                        // Find method name from configuration
                        $methodName = 'Unknown Method';
                        if (isset($restAPI->configuration_array['parameters'])) {
                            foreach ($restAPI->configuration_array['parameters'] as $method) {
                                if (isset($method['id']) && $method['id'] == $methodId) {
                                    $methodName = isset($method['name']) ? $method['name'] : $methodId;
                                    break;
                                }
                            }
                        }
                        
                        if (!in_array($methodName, $methodsUsed)) {
                            $methodsUsed[] = $methodName;
                        }
                    }
                }
                
                if (!empty($methodsUsed)) {
                    $items[] = [
                        'id' => $trigger['id'],
                        'bot_id' => $trigger['bot_id'],
                        'bot_name' => (string)erLhcoreClassModelGenericBotBot::fetch($trigger['bot_id']),
                        'name' => $trigger['name'],
                        'type' => 'trigger',
                        'methods' => $methodsUsed
                    ];
                }
            }
        }
        
        $tpl->set('items', $items);
        $tpl->set('rest_api', $restAPI);
    } else {
        $tpl->set('items', []);
        $tpl->set('rest_api', null);
    }
    
    echo $tpl->fetch();

} else if ($Params['user_parameters']['type'] == 'trigger') {


    if (isset($_POST['chat_id'])){

        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($Params['user_parameters']['id']);
        $actionID = $Params['user_parameters_unordered']['arg1'];

        foreach ($trigger->actions_front as $action) {
            if ($action['_id'] == $actionID) {
                $actionToExecute = $action;
                break;
            }
        }

        if ($actionToExecute) {
            $chat = erLhcoreClassModelChat::fetch($_POST['chat_id']);
            $msg = erLhcoreClassGenericBotActionText::process($chat, $actionToExecute, $trigger, ['do_not_save' => true]);
            echo json_encode(array('output' => htmlspecialchars($msg->msg)));
            exit;
        }

    } else {
        $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/usecasestrigger.tpl.php');
        $tpl->set('trigger_id', (int)$Params['user_parameters']['id']);
        $tpl->set('action_id', $Params['user_parameters_unordered']['arg1']);
        echo $tpl->fetch();
    }
    

} else {

    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/usecases.tpl.php');
    $db = ezcDbInstance::get();

    $search = '';
    $conditionIdentifier = '';

    if ($Params['user_parameters']['type'] == 'replace') {
        $replace = erLhcoreClassModelCannedMsgReplace::fetch((int)$Params['user_parameters']['id']);
        $search = '{' . $replace->identifier . '}';
    } else if ($Params['user_parameters']['type'] == 'condition') {
        $conditions = \LiveHelperChat\Models\Bot\Condition::fetch((int)$Params['user_parameters']['id']);
        $search = '{condition.' . $conditions->identifier . '}';
        $conditionIdentifier = $conditions->identifier;
    } else if ($Params['user_parameters']['type'] == 'tritem') {
        $translation = erLhcoreClassModelGenericBotTrItem::fetch((int)$Params['user_parameters']['id']);
        $search = '{' . $translation->identifier . '__';
    }

    $customSQL = 'SELECT id, \'translation\' AS \'type\' FROM `lh_generic_bot_tr_item` WHERE `translation` LIKE ' . $db->quote('%' . $search . '%') . ' OR `identifier` LIKE ' . $db->quote('%' . $search . '%') . '
UNION 
SELECT id, \'trigger\' AS \'type\' FROM `lh_generic_bot_trigger` WHERE `name` LIKE ' . $db->quote('%' . $search . '%') . ' OR `actions` LIKE ' . $db->quote('%' . $search . '%') . (!empty($conditionIdentifier) ? ' OR `actions` LIKE ' . $db->quote('%"trigger_condition":"' . $conditionIdentifier . '"%') . ' OR `actions` LIKE ' . $db->quote('%"trigger_condition":"-' . $conditionIdentifier . '"%') : '') . '
UNION
SELECT id, \'priority\' AS \'type\' FROM `lh_abstract_chat_priority` WHERE `value` LIKE ' . $db->quote('%' . $search . '%') . ' OR `role_destination` LIKE ' . $db->quote('%' . $search . '%') . ' OR `present_role_is` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'replace\' AS \'type\' FROM `lh_canned_msg_replace` WHERE `default` LIKE ' . $db->quote('%' . $search . '%') . ' OR `identifier` LIKE ' . $db->quote('%' . $search . '%') . ' OR `conditions` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'cannedmsg\' AS \'type\' FROM `lh_canned_msg` WHERE `msg` LIKE ' . $db->quote('%' . $search . '%') . ' OR `title` LIKE ' . $db->quote('%' . $search . '%') . ' OR `fallback_msg` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'mail_template\' AS \'type\' FROM `lhc_mailconv_response_template` WHERE `template` LIKE ' . $db->quote('%' . $search . '%') . ' OR `template_plain` LIKE ' . $db->quote('%' . $search . '%') . ' OR `name` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'bot_condition\' AS \'type\' FROM `lh_bot_condition` WHERE `configuration` LIKE ' . $db->quote('%' . $search . '%') . ' OR `name` LIKE ' . $db->quote('%' . $search . '%') . ' OR `identifier` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'webhook\' AS \'type\' FROM `lh_webhook` WHERE `name` LIKE ' . $db->quote('%' . $search . '%') . ' OR `event` LIKE ' . $db->quote('%' . $search . '%') . ' OR `configuration` LIKE ' . $db->quote('%' . $search . '%') . ' OR `status` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'auto_responder\' AS \'type\' FROM `lh_abstract_auto_responder` WHERE `wait_message` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_message` LIKE ' . $db->quote('%' . $search . '%') . ' OR `bot_configuration` LIKE ' . $db->quote('%' . $search . '%') . ' OR `name` LIKE ' . $db->quote('%' . $search . '%') . ' OR `operator` LIKE ' . $db->quote('%' . $search . '%') . ' OR `siteaccess` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_message_2` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_message_3` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_message_4` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_message_5` LIKE ' . $db->quote('%' . $search . '%') . ' OR `wait_timeout_hold` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_hold_message_1` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_hold_message_2` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_hold_message_3` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_hold_message_4` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_hold_message_5` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_reply_message_1` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_reply_message_2` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_reply_message_3` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_reply_message_4` LIKE ' . $db->quote('%' . $search . '%') . ' OR `timeout_reply_message_5` LIKE ' . $db->quote('%' . $search . '%') . ' OR `languages` LIKE ' . $db->quote('%' . $search . '%') . '
UNION
SELECT id, \'proactive_invitation\' AS \'type\' FROM `lh_abstract_proactive_chat_invitation` WHERE `message` LIKE ' . $db->quote('%' . $search . '%') . ' OR `message_returning` LIKE ' . $db->quote('%' . $search . '%') . ' OR `name` LIKE ' . $db->quote('%' . $search . '%') . ' OR `design_data` LIKE ' . $db->quote('%' . $search . '%');

    $items = $db->query($customSQL)->fetchAll(PDO::FETCH_ASSOC);

    $tpl->set('items', $items);
    $tpl->set('search', $search);

    echo $tpl->fetch();
}

exit;

?>