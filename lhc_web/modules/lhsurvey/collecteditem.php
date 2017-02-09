<?php 

$item = erLhAbstractModelSurveyItem::fetch((int)$Params['user_parameters']['id']);

$tpl = erLhcoreClassTemplate::getInstance('lhsurvey/collecteditem.tpl.php');
$tpl->set('survey_item',$item);
$tpl->set('survey',erLhAbstractModelSurvey::fetch($item->survey_id));

echo $tpl->fetch();
exit;

?>