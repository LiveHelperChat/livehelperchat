<?php

$lastMessageID = 0;
$lastOperatorChanged = false;
$lastOperatorId = false;
$lastOperatorNick = '';

$messagesStats = array(
    'total_messages' => count($messages),
    'counter_messages' => 0,
);

foreach ($messages as $msg) :
    $messagesStats['counter_messages']++;
    if ($lastOperatorId !== false && ($lastOperatorId != $msg['user_id'] || $msg['name_support'] != $lastOperatorNick)) {
        $lastOperatorChanged = true;
    } else {
        $lastOperatorChanged = false;
    }
    $lastOperatorId = $msg['user_id'];
    $lastOperatorNick = $msg['name_support'];
    ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
[[MESSAGE_SEPARATOR]]
<?php $lastMessageID = $msg['id']; endforeach; ?>