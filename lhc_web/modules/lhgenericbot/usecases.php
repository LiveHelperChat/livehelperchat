<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/usecases.tpl.php');

$db = ezcDbInstance::get();

$search = '';

if ($Params['user_parameters']['type'] == 'replace') {
    $replace = erLhcoreClassModelCannedMsgReplace::fetch((int)$Params['user_parameters']['id']);
    $search = '{' . $replace->identifier . '}';
} else if ($Params['user_parameters']['type'] == 'condition') {
    $conditions = \LiveHelperChat\Models\Bot\Condition::fetch((int)$Params['user_parameters']['id']);
    $search = '{condition.' . $conditions->identifier . '}';
} else if ($Params['user_parameters']['type'] == 'tritem') {
    $translation = erLhcoreClassModelGenericBotTrItem::fetch((int)$Params['user_parameters']['id']);
    $search = '{' . $translation->identifier . '__';
}

$customSQL = 'SELECT id, \'translation\' AS \'type\' FROM `lh_generic_bot_tr_item` WHERE `translation` LIKE ' . $db->quote('%' . $search . '%') . ' OR `identifier` LIKE ' . $db->quote('%' . $search . '%') . '
UNION 
SELECT id, \'trigger\' AS \'type\' FROM `lh_generic_bot_trigger` WHERE `name` LIKE ' . $db->quote('%' . $search . '%') . ' OR `actions` LIKE ' . $db->quote('%' . $search . '%') . '
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
exit;

?>