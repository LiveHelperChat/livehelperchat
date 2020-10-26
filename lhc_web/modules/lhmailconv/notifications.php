<?php
$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/notifications.tpl.php');

if (ezcInputForm::hasPostData()) {

    $definition = array(
        'malarm_pr' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'malarm_p' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

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

    $tpl->set('updated', true);
}

echo $tpl->fetch();
exit();

?>