<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/merge.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);
$inputData = [
    'merge_destination' => $mail->id,
    'source_mail' => [],
];

if (ezcInputForm::hasPostData($mail)) {

    $definition = array(
        'merge_destination' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
        ),
        'source_mail' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ($form->hasValidData('merge_destination')) {
        $mergeDestination = erLhcoreClassModelMailconvConversation::fetch($form->merge_destination);
        $inputData['merge_destination'] = $form->merge_destination;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose a merge destination!');
    }

    if ($form->hasValidData('source_mail')) {
        $sourceDestinations = erLhcoreClassModelMailconvConversation::getList(['filterin' => ['id' => $form->source_mail]]);
        $inputData['source_mail'] = $form->source_mail;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose what mails you want to merge!');
    }

    if (empty($Errors)) {
        try {
            LiveHelperChat\mailConv\helpers\MergeHelper::merge($mergeDestination, $sourceDestinations, ['user_id' => $currentUser->getUserID(), 'name_support' => $currentUser->getUserData()->name_support]);
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', [$e->getMessage()]);
        }
    } else {
        $tpl->set('errors', $Errors);
    }

}

$tpl->set('mail', $mail);
$tpl->set('input_data', $inputData);

print $tpl->fetch();
exit;

?>