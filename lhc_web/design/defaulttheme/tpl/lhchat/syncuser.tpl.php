<?php
$lastOperatorChanged = false;
$lastOperatorId = false;
$lastOperatorNick = '';

$messagesStats = array(
    'total_messages' => count($messages),
    'counter_messages' => 0,
);

$messageDateFormatDay = erLhcoreClassModule::$dateDateHourFormat;
$messageDateFormatDayTime = erLhcoreClassModule::$dateHourFormat;

if (isset($theme) && is_object($theme)) {
    if (isset($theme->bot_configuration_array['msg_time_format_day']) && !empty($theme->bot_configuration_array['msg_time_format_day'])){
        $messageDateFormatDay = $theme->bot_configuration_array['msg_time_format_day'];
    }
    if (isset($theme->bot_configuration_array['msg_time_format_time']) && !empty($theme->bot_configuration_array['msg_time_format_time'])){
        $messageDateFormatDayTime = $theme->bot_configuration_array['msg_time_format_time'];
    }
}

foreach ($messages as $msg) :
    $messagesStats['counter_messages']++;

if ($lastOperatorId !== false && ($lastOperatorId != $msg['user_id'] || $msg['name_support'] != $lastOperatorNick)) {
    $lastOperatorChanged = true;
    $lastOperatorNick = $msg['name_support'];
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg['user_id'];
$lastOperatorNick = $msg['name_support'];

if ($msg['meta_msg'] == '') {
    $msg['meta_msg'] = '{}';
}

?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
<?php endforeach; ?>

