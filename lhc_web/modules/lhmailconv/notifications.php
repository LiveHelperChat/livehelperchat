<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/notifications.tpl.php');

if (ezcInputForm::hasPostData()) {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $definition = array(
        'malarm_pr' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'malarm_p' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'malarm_h' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'subject_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',  array('min_range' => 1), FILTER_REQUIRE_ARRAY)
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('subject_id')) {
        erLhcoreClassModelUserSetting::setSetting('subject_mail_id', json_encode($form->subject_id));
    } else {
        erLhcoreClassModelUserSetting::setSetting('subject_mail_id', '[]');
    }

    if ($form->hasValidData('malarm_pr')) {
        erLhcoreClassModelUserSetting::setSetting('malarm_pr', $form->malarm_pr);
    } else {
        erLhcoreClassModelUserSetting::setSetting('malarm_pr', -1);
    }

    if ($form->hasValidData('malarm_p')) {
        erLhcoreClassModelUserSetting::setSetting('malarm_p', $form->malarm_p);
    } else {
        erLhcoreClassModelUserSetting::setSetting('malarm_p', -1);
    }

    if ($form->hasValidData('malarm_h')) {
        erLhcoreClassModelUserSetting::setSetting('malarm_h', $form->malarm_h);
    } else {
        erLhcoreClassModelUserSetting::setSetting('malarm_h', -1);
    }

    $tpl->set('updated', true);
}

$tpl->setArray(array('subject_id' => json_decode(erLhcoreClassModelUserSetting::getSetting('subject_mail_id','[]'), true)));

echo $tpl->fetch();
exit();

?>