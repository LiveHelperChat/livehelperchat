<?php

$tpl = erLhcoreClassTemplate::getInstance('lhcannedmsg/subject.tpl.php');
$canned = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters']['canned_id']);

if ($canned instanceof erLhcoreClassModelCannedMsg)
{
    if (ezcInputForm::hasPostData()) {

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
        echo json_encode($response);
        exit;
    }

    if (isset($_GET['getsubjects'])) {
        $tpl = erLhcoreClassTemplate::getInstance('lhcannedmsg/getsubjects.tpl.php');
        $tpl->set('canned', erLhcoreClassModelCannedMsg::fetch($Params['user_parameters']['canned_id']));
        echo $tpl->fetch();
        exit;
        /*$canned = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters']['canned_id']);*/
    }

    $tpl->set('canned', $canned);
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