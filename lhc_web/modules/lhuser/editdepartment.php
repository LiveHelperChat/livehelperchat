<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/editdepartment.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);
$dep = erLhcoreClassModelDepartament::fetch($Params['user_parameters']['dep_id']);

if ($user instanceof erLhcoreClassModelUser && $dep instanceof erLhcoreClassModelDepartament)
{
    if (ezcInputForm::hasPostData()) {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $response = array('error' => true, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Invalid CSRF token'));
        }

        $db = ezcDbInstance::get();
        $db->beginTransaction();
        $response = array();
        if (is_numeric($Params['user_parameters_unordered']['subject'])) {
            if ($Params['user_parameters_unordered']['status'] == 'true') {
                $subjectChat = erLhcoreClassModelCannedMsgSubject::findOne(array('filter' => array('canned_id' => $canned->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                if (!($subjectChat instanceof erLhcoreClassModelCannedMsgSubject)) {
                    $subjectChat = new erLhcoreClassModelCannedMsgSubject();
                }

                $subjectChat->canned_id = $canned->id;
                $subjectChat->subject_id = $Params['user_parameters_unordered']['subject'];
                $subjectChat->saveThis();

                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject selected!'));

            } else {
                $subjectChat = erLhcoreClassModelCannedMsgSubject::findOne(array('filter' => array('canned_id' => $canned->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                if ($subjectChat instanceof erLhcoreClassModelCannedMsgSubject) {
                    $subjectChat->removeThis();
                }

                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject unselected!'));
            }
        } else {
            $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Please choose a subject'));
        }

        $db->commit();
        header('Content-type: application/json');
        echo json_encode($response);
        exit;
    }

    $tpl->set('user', $user);
    $tpl->set('dep', $dep);
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    $tpl->set('auto_close_dialog',true);
    $tpl->set('chat_id',(int)$Params['user_parameters']['dep_id']);
    echo $tpl->fetch();
    exit;
}

exit;

?>