<?php
$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/notifications.tpl.php');

if (ezcInputForm::hasPostData()) {

    $definition = array(
        'bot_notifications' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean'),
        'bot_msg_nm' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('bot_notifications')) {
        erLhcoreClassModelUserSetting::setSetting('bot_notifications', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('bot_notifications', 0);
    }

    if ($form->hasValidData('bot_msg_nm')) {
        erLhcoreClassModelUserSetting::setSetting('bot_msg_nm', $form->bot_msg_nm);
    } else {
        erLhcoreClassModelUserSetting::setSetting('bot_msg_nm', 3);
    }

    $tpl->set('updated', true);
}


$tpl->setArray(array('numberOfMessages' => 3));

echo $tpl->fetch();
exit();

?>