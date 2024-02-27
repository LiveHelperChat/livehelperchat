<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/view.tpl.php');

$item =  erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

$tpl->setArray(array(
    'item' => $item
));

$Result['content'] = $tpl->fetch();

?>