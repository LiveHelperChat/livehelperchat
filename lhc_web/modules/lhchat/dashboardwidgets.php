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

// Exclude notifications icons
$dwic = json_decode(erLhcoreClassModelUserSetting::getSetting('dwic', ''),true);

if ($dwic === null) {
    $dwic = [];
}

// Exclude notifications icons
$notif_icons = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_nic', ''),true);

if ($notif_icons === null) {
    $notif_icons = [];
}

$supportedWidgets = array();
$supportedWidgets['online_operators'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online operators');
$supportedWidgets['active_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Active chats');
$supportedWidgets['online_visitors'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online visitors');
$supportedWidgets['departments_stats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Departments stats');
$supportedWidgets['pending_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Pending chats');
$supportedWidgets['transfered_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Transfered chats');

if (erLhcoreClassUser::instance()->hasAccessTo('lhgroupchat', 'use')) {
    $supportedWidgets['group_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Group chats');
}

if (erLhcoreClassModelChatConfig::fetchCache('list_unread')->current_value == 1) {
    $supportedWidgets['unread_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Unread chats');
}

if (erLhcoreClassModelChatConfig::fetchCache('list_closed')->current_value == 1) {
    $supportedWidgets['closed_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Closed chats');
}

$supportedWidgets['my_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','My active and pending chats');
$supportedWidgets['bot_chats'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Bot chats');

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.dashboardwidgets',array('supported_widgets' => & $supportedWidgets));

if (ezcInputForm::hasPostData()) {
    $definition = array(
        'WidgetsUser' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY),
        'ColumnNumber' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'exclude_icon' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY),
        'notif_icons' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY)
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

        if ($form->ColumnNumber !== count($dashboardOrder)) {
            if ($form->ColumnNumber > count($dashboardOrder)) {
                for ($i = $form->ColumnNumber - count($dashboardOrder); $i > 0; $i--) {
                    $dashboardOrder[] = array();
                }
            } elseif ($form->ColumnNumber < count($dashboardOrder)) {
                $dashboardRemoved = array_splice($dashboardOrder,$form->ColumnNumber);

                foreach ($dashboardRemoved as $items) {
                    foreach ($items as $item) {
                        $dashboardOrder[0][] = $item;
                    }
                }
            }
        }

        // Store settings in user scope now
        erLhcoreClassModelUserSetting::setSetting('dwo', json_encode(array_values($dashboardOrder)));

        $tpl->set('updated', true);
    }

    if ($form->hasValidData('exclude_icon')) {
        $dwic = array_values($form->exclude_icon);
        erLhcoreClassModelUserSetting::setSetting('dwic', json_encode($dwic));
    } else {
        $dwic = [];
        erLhcoreClassModelUserSetting::setSetting('dwic', json_encode($dwic));
    }

    if ($form->hasValidData('notif_icons')) {
        $notif_icons = array_values($form->notif_icons);
        erLhcoreClassModelUserSetting::setSetting('dw_nic', json_encode($notif_icons));
    } else {
        $notif_icons = [];
        erLhcoreClassModelUserSetting::setSetting('dw_nic', json_encode($notif_icons));
    }
}

$tpl->setArray(array(
    'widgets' => $supportedWidgets,
    'user_widgets' => $widgetsUser,
    'columns_number' => count($dashboardOrder),
    'exclude_icons' => $dwic,
    'notif_icons' => $notif_icons,
));

echo $tpl->fetch();
exit();

?>