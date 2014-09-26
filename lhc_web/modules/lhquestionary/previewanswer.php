<?php

$tpl = erLhcoreClassTemplate::getInstance('lhquestionary/previewanswer.tpl.php');
$answer = erLhcoreClassModelQuestionAnswer::fetch((int)$Params['user_parameters']['id']);

$tpl->set('answer',$answer);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>