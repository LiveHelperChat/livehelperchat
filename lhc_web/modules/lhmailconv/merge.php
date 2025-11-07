<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/merge.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

// Check read and write access to the main mail
if (!($mail instanceof erLhcoreClassModelMailconvConversation) || !erLhcoreClassChat::hasAccessToRead($mail)) {
    erLhcoreClassModule::redirect('mailconv/conversations');
    exit;
}

$canWrite = erLhcoreClassChat::hasAccessToWrite($mail);
if (!$canWrite) {
    erLhcoreClassModule::redirect('mailconv/conversations');
    exit;
}

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
        
        // Check read and write access to merge destination
        if (!($mergeDestination instanceof erLhcoreClassModelMailconvConversation)) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Invalid merge destination!');
        } elseif (!erLhcoreClassChat::hasAccessToRead($mergeDestination)) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read merge destination!');
        } elseif (!erLhcoreClassChat::hasAccessToWrite($mergeDestination)) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to write to merge destination!');
        } else {
            $inputData['merge_destination'] = $form->merge_destination;
        }
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose a merge destination!');
    }

    if ($form->hasValidData('source_mail')) {
        $sourceDestinations = erLhcoreClassModelMailconvConversation::getList(['filterin' => ['id' => $form->source_mail]]);
        
        // Check read and write access to all source mails
        foreach ($sourceDestinations as $sourceMail) {
            if (!erLhcoreClassChat::hasAccessToRead($sourceMail)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read mail') . ' #' . $sourceMail->id;
                break;
            }
            if (!erLhcoreClassChat::hasAccessToWrite($sourceMail)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to write to mail') . ' #' . $sourceMail->id;
                break;
            }
        }
        
        // Check merge_cross_departments permission if needed
        if (empty($Errors) && isset($mergeDestination)) {
            $allDepartments = [$mergeDestination->dep_id];
            foreach ($sourceDestinations as $sourceMail) {
                $allDepartments[] = $sourceMail->dep_id;
            }
            $uniqueDepartments = array_unique($allDepartments);
            
            if (count($uniqueDepartments) > 1 && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv', 'merge_cross_departments')) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to merge mails across different departments!');
            }
        }
        
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