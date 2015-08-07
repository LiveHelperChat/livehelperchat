<?php

// For IE to support headers if chat is installed on different domain
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

$tpl = erLhcoreClassTemplate::getInstance( 'lhsurvey/fillwidget.tpl.php');

$embedMode = false;
if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
	$embedMode = true;
}

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$tpl->set('theme',$theme);
	} catch (Exception $e) {

	}
}

try {
  
    if ((string)$Params['user_parameters_unordered']['hash'] != '') {
        list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);    
        try {
            $chat = erLhcoreClassModelChat::fetch($chatID);            
        } catch (Exception $e) {
    
        }    
    }
    
    erLhcoreClassChat::setTimeZoneByChat($chat);
    
    if ($chat->hash == $hash)
    {
        $survey = erLhAbstractModelSurvey::fetch($Params['user_parameters_unordered']['survey']);
        $surveyItem = erLhAbstractModelSurveyItem::getInstance($chat, $survey);
        
        if ( isset($_POST['Vote']) ) {
            $errors = erLhcoreClassSurveyValidator::validateSurvey($surveyItem, $survey);
            if (empty($errors)) {
                $surveyItem->saveOrUpdate();  
                $tpl->set('just_stored',true);
            } else {
                $tpl->set('errors',$errors);
            }
        }
           
        $tpl->set('chat',$chat);
        $tpl->set('survey',$survey);
        $tpl->set('survey_item',$surveyItem);
        
        $Result['chat'] = $chat;       

    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['pagelayout_css_append'] = 'widget-chat';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_chat';
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chat started')));
$Result['is_sync_required'] = false;

if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_chat_page';
	$Result['pagelayout_css_append'] = 'embed-widget';
}

?>