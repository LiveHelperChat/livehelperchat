<?php
$lastOperatorChanged = false;
$lastOperatorId = false;

$messagesStats = array(
    'total_messages' => count($messages),
    'counter_messages' => 0,
);

foreach ($messages as $msg) :
    $messagesStats['counter_messages']++;

if ($lastOperatorId !== false && $lastOperatorId != $msg['user_id']) {
    $lastOperatorChanged = true;
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg['user_id'];

?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
<?php endforeach; ?>

