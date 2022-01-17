<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/subjectwidget.tpl.php');

if (ezcInputForm::hasPostData()) {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        die('Invalid CSRF token!');
    }

    $definition = array(
        'subject_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',  array('min_range' => 1), FILTER_REQUIRE_ARRAY)
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('subject_id')) {
        erLhcoreClassModelUserSetting::setSetting('subject_id', json_encode($form->subject_id));
    } else {
        erLhcoreClassModelUserSetting::setSetting('subject_id', '[]');
    }

    $tpl->set('updated', true);
}

$tpl->setArray(array('subject_id' => json_decode(erLhcoreClassModelUserSetting::getSetting('subject_id','[]'), true)));

echo $tpl->fetch();
exit();

?>