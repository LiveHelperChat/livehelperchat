<?php 
$lastOperatorChanged = false;
$lastOperatorId = 0;

foreach ($messages as $msg) : 

if ($msg['user_id'] > 0 && $lastOperatorId > 0 && $lastOperatorId != $msg['user_id']) {
    $lastOperatorChanged = true;
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg['user_id'];

?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>			            
<?php endforeach; ?>