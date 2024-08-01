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

foreach ($messages as $msg) :
    $msg = $msg->getState();
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