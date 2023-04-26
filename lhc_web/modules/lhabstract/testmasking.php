<?php

$tpl = erLhcoreClassTemplate::getInstance('lhabstract/custom/testmasking.tpl.php');
$tpl->set('mask',(isset($_POST['mask']) ? $_POST['mask'] : ''));
$tpl->set('messages',(isset($_POST['messages']) ? $_POST['messages'] : ''));
$tpl->set('output','');

if (isset($_POST['messages'])) {
    $maskingObject = new \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting();
    $maskingObject->pattern = isset($_POST['mask']) ? $_POST['mask'] : '';
    $tpl->set('output',$maskingObject->getMasked($_POST['messages']));
}

echo $tpl->fetch();
exit;

?>