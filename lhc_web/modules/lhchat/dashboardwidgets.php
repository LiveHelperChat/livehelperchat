<?php
$tpl = erLhcoreClassTemplate::getInstance('lhchat/dashboardwidgets.tpl.php');

$dashboardOrderString = (string) erLhcoreClassModelUserSetting::getSetting('dwo', '');

if (empty($dashboardOrderString)) {
    $dashboardOrderString = erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value;
}

$widgetsUser = array();

$dashboardOrder = array_filter(explode('|', $dashboardOrderString));

foreach ($dashboardOrder as $widgetsColumn) {
    $widgetsColumnItems = array_filter(explode(',', $widgetsColumn));
    foreach ($widgetsColumnItems as $widget) {
        $widgetsUser[] = $widget;
    }
}

$supportedWidgets = array(
    'online_operators' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online operators'),
    'active_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Active chats'),
    'online_visitors' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online visitors'),
    'departments_stats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Departments stats'),
    'pending_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Pending chats'),
    'unread_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Unread chats'),
    'transfered_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Transfered chats'),
    'closed_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Closed chats')
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.dashboardwidgets',array('supported_widgets' => & $supportedWidgets));

if (ezcInputForm::hasPostData()) {
    $definition = array(
        'WidgetsUser' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY)
    );
    
    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();
    
    if ($form->hasValidData('WidgetsUser') && ! empty($form->WidgetsUser)) {
        
        // Add new widgets
        foreach ($form->WidgetsUser as $newUserWidget) {
            if (! in_array($newUserWidget, $widgetsUser)) {
                $dashboardOrderString = $newUserWidget . ',' . $dashboardOrderString;
                $widgetsUser[] = $newUserWidget;
            }
        }
        
        // Remove removed widgets
        foreach ($widgetsUser as $userWidget) {
            if (! in_array($userWidget, $form->WidgetsUser)) {
                $dashboardOrderString = str_replace($userWidget, '', $dashboardOrderString);
                unset($widgetsUser[array_search($userWidget, $widgetsUser)]);
            }
        }
        
        // Just cleanup
        $dashboardOrderString = str_replace(array(
            ',,',
            ',,,',
            ',,,,',
            '|,',
            ',|'
        ), array(
            ',',
            ',',
            ',',
            '|',
            '|'
        ), $dashboardOrderString);
        
        // Store settings in user scope now
        erLhcoreClassModelUserSetting::setSetting('dwo', $dashboardOrderString);
        
        $tpl->set('updated', true);
    }
}

$tpl->setArray(array(
    'widgets' => $supportedWidgets,
    'user_widgets' => $widgetsUser
));

echo $tpl->fetch();
exit();

?>