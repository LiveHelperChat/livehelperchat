<?php
$question = erLhcoreClassQuestionary::getSession()->load( 'erLhcoreClassModelQuestion', $Params['user_parameters']['id']);
$question->removeThis();

erLhcoreClassModule::redirect('questionary/list');
exit;
?>