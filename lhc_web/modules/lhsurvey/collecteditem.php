<?php 

$item = erLhAbstractModelSurveyItem::fetch((int)$Params['user_parameters']['id']);

$survey = erLhAbstractModelSurvey::fetch($item->survey_id);

if ( $survey->checkPermission() === false ) {
    die('No permission to read results');
    exit;
} else {
    $tpl = erLhcoreClassTemplate::getInstance('lhsurvey/collecteditem.tpl.php');
    $tpl->set('survey_item',$item);
    $tpl->set('survey',$survey);
}

echo $tpl->fetch();


exit;

?>