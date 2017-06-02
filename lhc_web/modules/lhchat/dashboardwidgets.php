<?php
$tpl = erLhcoreClassTemplate::getInstance('lhchat/dashboardwidgets.tpl.php');

$dashboardOrder = json_decode(erLhcoreClassModelUserSetting::getSetting('dwo', ''),true);

if ($dashboardOrder === null) {
	$dashboardOrder = json_decode(erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value,true);
}

$widgetsUser = array();
foreach ($dashboardOrder as $widgetsColumn) {
    foreach ($widgetsColumn as $widget) {
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
    'closed_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Closed chats'),
    'my_chats' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','My active and pending chats')
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
                $dashboardOrder[0][] = $newUserWidget;
                $widgetsUser[] = $newUserWidget;
            }
        }

        // Remove removed widgets
        foreach ($widgetsUser as $userWidget) {
            if (! in_array($userWidget, $form->WidgetsUser)) {
                foreach ($dashboardOrder as $key => $widgetsColumn) {
                	if (in_array($userWidget, $widgetsColumn)) {
                		unset($widgetsColumn[array_search($userWidget, $widgetsColumn)]);
                		$dashboardOrder[$key] = $widgetsColumn;
                	}
                }
                unset($widgetsUser[array_search($userWidget, $widgetsUser)]);
            }
        }
        if (count($dashboardOrder) == 1) {
            $dashboardOrder[] = array();
            $dashboardOrder[] = array();
        }  
              
        if (count($dashboardOrder) == 2) {
            $dashboardOrder[] = array();
        }        
        // Store settings in user scope now
        erLhcoreClassModelUserSetting::setSetting('dwo', json_encode($dashboardOrder));
        
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