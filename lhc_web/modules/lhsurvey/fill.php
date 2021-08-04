<?php

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$themeAppend = '/(theme)/'.$theme->id;
	} catch (Exception $e) {

	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
			$Result['theme'] = $theme;			
		} catch (Exception $e) {
			
		}
	}
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhsurvey/fill.tpl.php');

if (isset($Result['theme'])){
    $tpl->set('theme',$Result['theme']);
}

try {

    if ((string)$Params['user_parameters_unordered']['hash'] != '') {
        $hash = $Params['user_parameters_unordered']['hash'];        
    }

    if (is_numeric($Params['user_parameters_unordered']['chatid'])) {
        $chat = erLhcoreClassModelChat::fetch($Params['user_parameters_unordered']['chatid']);
    }

    erLhcoreClassChat::setTimeZoneByChat($chat);

    $chatVariables = $chat->chat_variables_array;
    
    if (erLhcoreClassModelChatBlockedUser::isBlocked(array('ip' => $chat->ip, 'dep_id' => $chat->dep_id, 'nick' => $chat->nick, 'email' => $chat->email)) || (isset($chatVariables['lhc_ds']) && (int)$chatVariables['lhc_ds'] == 0)) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','At this moment you can contact us via email only. Sorry for the inconveniences.'));
    }

    if ($chat->hash == $hash)
    {
        $survey = erLhAbstractModelSurvey::fetch($Params['user_parameters_unordered']['survey']);
        $surveyItem = erLhAbstractModelSurveyItem::getInstance($chat, $survey);

        if ( isset($_POST['Vote']) ) {
            $errors = erLhcoreClassSurveyValidator::validateSurvey($surveyItem, $survey);
            
            
            if (empty($errors)) {
                $surveyItem->saveOrUpdate();
                
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('survey.filled', array('chat' => & $chat, 'survey' => $survey, 'survey_item' => & $surveyItem));
                
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
$Result['pagelayout'] = 'userchat';
$Result['show_switch_language'] = true;

?>