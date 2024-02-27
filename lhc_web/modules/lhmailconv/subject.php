<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/subject.tpl.php');
$item = erLhcoreClassModelMailconvResponseTemplate::fetch($Params['user_parameters']['id']);

if ($item instanceof erLhcoreClassModelMailconvResponseTemplate)
{
    if (ezcInputForm::hasPostData() && isset($_SERVER['HTTP_X_CSRFTOKEN']) && $currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        $db = ezcDbInstance::get();
        $db->beginTransaction();
        $response = array();
        if (is_numeric($Params['user_parameters_unordered']['subject'])) {
            if ($Params['user_parameters_unordered']['status'] == 'true') {
                $subjectChat = erLhcoreClassModelMailconvResponseTemplateSubject::findOne(array('filter' => array('template_id' => $item->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                if (!($subjectChat instanceof erLhcoreClassModelMailconvResponseTemplateSubject)) {
                    $subjectChat = new erLhcoreClassModelMailconvResponseTemplateSubject();
                }

                $subjectChat->template_id = $item->id;
                $subjectChat->subject_id = $Params['user_parameters_unordered']['subject'];
                $subjectChat->saveThis();

                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject selected!'));

            } else {
                $subjectChat = erLhcoreClassModelMailconvResponseTemplateSubject::findOne(array('filter' => array('template_id' => $item->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                if ($subjectChat instanceof erLhcoreClassModelMailconvResponseTemplateSubject) {
                    $subjectChat->removeThis();
                }

                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject unselected!'));
            }
        } else {
            $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Please choose a subject'));
        }

        $db->commit();
        echo json_encode($response);
        exit;
    }

    if (isset($_GET['getsubjects'])) {
        $tpl = erLhcoreClassTemplate::getInstance('lhmailconv/getsubjects.tpl.php');
        $tpl->set('item', erLhcoreClassModelMailconvResponseTemplate::fetch($Params['user_parameters']['id']));
        echo $tpl->fetch();
        exit;
    }

    $tpl->set('item', $item);
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    $tpl->set('auto_close_dialog',true);
    $tpl->set('chat_id',(int)$Params['user_parameters']['chat_id']);
    echo $tpl->fetch();
    exit;
}

exit;

?>