<?php
$messagesDefault = [];
foreach ($messages as $msg) {
    $messagesDefault[] = $msg->getState();
}
$messages = $messagesDefault;
?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>