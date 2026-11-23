<?php

session_write_close();

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/sendemail.tpl.php');

$item = new erLhcoreClassModelMailconvMessage();

$chat = null;

$isEscalate = isset($Params['user_parameters_unordered']['escalate']) && $Params['user_parameters_unordered']['escalate'] == 1 && $currentUser->hasAccessTo('lhmailconv','email_escalation');
$tpl->set('is_escalate', $isEscalate);

if (is_numeric($Params['user_parameters_unordered']['chat_id'])) {

    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters_unordered']['chat_id']);

    if (!erLhcoreClassChat::hasAccessToRead($chat) || !erLhcoreClassChat::hasAccessToWrite($chat)) {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
        echo $tpl->fetch();
        exit;
    }

    $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['active' => 1, 'mail' => $chat->department->email]]);

    if (is_object($mailbox)) {
        $item->mailbox_id = $mailbox->id;
        $item->mailbox_front = $mailbox->mail;
    }

    if (!$isEscalate) {
        $item->from_address = $chat->email;
        $item->from_name = $chat->nick;
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.new_mail_from_chat', array(
        'uparams' => $Params['user_parameters_unordered'],
        'msg' => & $item,
        'chat' => & $chat,
        'tpl' => & $tpl
    ));

    $tpl->set('chat',$chat);
} else {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.new_mail_from_vars', array(
        'uparams' => $Params['user_parameters_unordered'],
        'msg' => & $item,
        'chat' => & $chat,
        'tpl' => & $tpl
    ));
}

$tpl->set('uparams',$Params['user_parameters_unordered']);

// Prefill form with the content of a previously sent e-mail
$copyRequest = isset($_POST['copy_content']) && is_string($_POST['copy_content']);
$copyData = null;

if ($copyRequest) {
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $tpl->set('errors', array('Invalid CSRF token!'));
    } else {
        $copyData = json_decode($_POST['copy_content'], true);

        if (is_array($copyData)) {
            $item->subject = (string)($copyData['subject'] ?? '');
            $item->from_name = (string)($copyData['from_name'] ?? '');
            $item->to_data = (string)($copyData['to_data'] ?? '');
            $item->reply_to_data = (string)($copyData['reply_to_data'] ?? '');
            $item->body = (string)($copyData['body'] ?? '');
            $item->mailbox_front = (string)($copyData['mailbox_front'] ?? '');

            if ($currentUser->hasAccessTo('lhmailconv','send_mail_bcc_cc') && isset($copyData['cc']) && is_array($copyData['cc'])) {
                $item->cc_data = json_encode(array_fill_keys($copyData['cc'], ''));
            }

            if ($currentUser->hasAccessTo('lhmailconv','send_mail_bcc_cc') && isset($copyData['bcc']) && is_array($copyData['bcc'])) {
                $item->bcc_data = json_encode(array_fill_keys($copyData['bcc'], ''));
            }

            if (isset($copyData['from_address'])) {
                $item->from_address = (string)$copyData['from_address'];
            }
        }
    }
}

if (ezcInputForm::hasPostData() && $copyRequest === false) {

    $Errors = erLhcoreClassMailconvValidator::validateNewEmail($item, $chat, ['escalate' => $isEscalate]);

    // Custom CC/BCC recipients require a separate permission
    if (!$currentUser->hasAccessTo('lhmailconv','send_mail_bcc_cc')) {
        $item->cc_data = '';
        $item->bcc_data = '';
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF token!';
    }

    // Do not send an e-mail if it contains sensitive information
    if ((int)erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value == 1 && $item->body != '') {

        $depId = 0;
        if ($chat instanceof erLhcoreClassModelChat) {
            $depId = $chat->dep_id;
        } elseif ($item->mailbox instanceof erLhcoreClassModelMailconvMailbox) {
            $depId = $item->mailbox->dep_id;
        }

        $maskResult = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessageHtml($item->body, [
            'dep_id' => $depId,
            'type' => \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_OPERATOR_EMAIL,
            'return_array' => true
        ]);

        if ($maskResult['message'] !== $item->body) {

            $matchedRules = array_map(['\\LiveHelperChat\\Helpers\\BBCode\\Mask', 'render'], \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::getMatchedEntities());

            $errorMessage = !empty($maskResult['warning'])
                ? htmlspecialchars($maskResult['warning'], ENT_QUOTES, 'UTF-8')
                : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','For privacy protection, the message was not sent because it contains sensitive information (PII).');

            $Errors[] = $errorMessage . (!empty($matchedRules) ? ' ' . implode(' ', $matchedRules) : '');
        }
    }

    if (empty($Errors)) {

        $response = array();
        $sendParams =  ['background' => true];

        // Remember sent content, so operator could reuse it in a new e-mail
        $copyParams = array(
            'subject' => (string)$item->subject,
            'from_name' => (string)$item->from_name,
            'to_data' => (string)$item->to_data,
            'reply_to_data' => (string)$item->reply_to_data,
            'cc' => array_column($item->cc_data_array, 'email'),
            'bcc' => array_column($item->bcc_data_array, 'email'),
            'body' => (string)$item->body,
            'mailbox_front' => (string)$item->mailbox_front,
        );

        if ($chat === null || $isEscalate || erLhcoreClassUser::instance()->hasAccessTo('lhchat','chat_see_unhidden_email')) {
            $copyParams['from_address'] = (string)$item->from_address;
        }

        try {
            
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.before_send', array(
                'uparams' => $Params['user_parameters_unordered'],
                'msg' => & $item,
                'chat' => & $chat,
                'tpl' => & $tpl,
                'send_params' => & $sendParams
            ));

            // Log user changes
            $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
            $data = (array)$auditOptions->data;

            if (isset($data['log_custom_reply']) && $data['log_custom_reply'] == 1 && !empty($item->to_data) && $item->to_data != $item->mailbox_front) {
                erLhcoreClassLog::logObjectChange(array(
                    'object' => $item,
                    'action' => '-newMessage',
                    'msg' => array(
                        'new' => $item,
                        'user_id' => $currentUser->getUserID()
                    )
                ));
            }

            erLhcoreClassMailconvValidator::sendEmail($item, $response, $currentUser->getUserID(), $sendParams);
        } catch (Exception $e) {
            $response['errors'][] = $e->getMessage();
        }

        if (isset($response['send']) && $response['send'] === true) {
            $tpl->set('updated',true);
            $tpl->set('outcome',$response);
            $tpl->set('item',$item);
            $tpl->set('copy_data', json_encode($copyParams, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
        } else {
            if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
                foreach($response['errors'] as $index => $error) {
                    $response['errors'][$index] = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage($error, array('dep_id' => 0));
                }
            }
            $tpl->set('errors',$response['errors']);
        }

    } else {
        $tpl->set('errors',$Errors);
    }

}

$department = null;
if ($chat instanceof erLhcoreClassModelChat && $chat->department instanceof erLhcoreClassModelDepartament) {
    $department = $chat->department;
} elseif ($item->dep_id > 0 && $item->department instanceof erLhcoreClassModelDepartament) {
    $department = $item->department;
} elseif ($item->mailbox instanceof erLhcoreClassModelMailconvMailbox && $item->mailbox->dep_id > 0) {
    $dep = erLhcoreClassModelDepartament::fetch($item->mailbox->dep_id, true);
    if ($dep instanceof erLhcoreClassModelDepartament) {
        $department = $dep;
    }
}

$tpl->setArray(array(
    'item' => $item,
    'department' => $department,
));

if (isset($Params['user_parameters_unordered']['layout']) && $Params['user_parameters_unordered']['layout'] == 'popup') {
    $Result['pagelayout'] = 'chattabs';
}

$tpl->set('Result',['popup' => isset($Result['pagelayout']), 'path' => array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/conversations'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
)]);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJSStatic('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';
$Result['additional_footer_js'] .= '<script src="'.erLhcoreClassDesign::designJS('js/sendemail.js').'"></script>';

?>