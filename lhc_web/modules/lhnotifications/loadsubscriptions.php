<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhnotifications/loadsubscriptions.tpl.php');

$items = \LiveHelperChat\Models\Notifications\OperatorSubscriber::getList(array('limit' => 100, 'filter' => array('user_id' => $currentUser->getUserID())));

$tpl->set('items',$items);
echo $tpl->fetch();

exit;

?>
