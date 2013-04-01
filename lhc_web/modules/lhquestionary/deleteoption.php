<?php

$question = erLhcoreClassModelQuestionOption::fetch((int)$Params['user_parameters']['id']);
$question->removeThis();

erLhcoreClassModule::redirect('questionary/edit',"/{$question->question_id}/(tab)/voting");
exit;
?>