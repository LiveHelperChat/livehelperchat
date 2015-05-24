<?php 
$lastOperatorChanged = false;
$lastOperatorId = false;

foreach ($messages as $msg) : 

if ($lastOperatorId !== false && $lastOperatorId != $msg['user_id']) {
    $lastOperatorChanged = true;
} else {
    $lastOperatorChanged = false;
}

$lastOperatorId = $msg['user_id'];

?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>			            
<?php endforeach; ?>