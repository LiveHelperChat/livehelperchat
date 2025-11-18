<?php

header('Content-Type: application/json');

$response = [
    'error' => true,
    'msg' => '',
    'signature' => ''
];

try {
    
    if (!isset($_POST['mailbox_email']) || empty($_POST['mailbox_email'])) {
        $response['msg'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt', 'Mailbox email is required');
        echo json_encode($response);
        exit;
    }
    
    $mailboxEmail = trim($_POST['mailbox_email']);
    
    // Fetch the mailbox by email
    $mailbox = erLhcoreClassModelMailconvMailbox::findOne([
        'filter' => [
            'mail' => $mailboxEmail,
            'active' => 1
        ]
    ]);
    
    if (!$mailbox) {
        $response['msg'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt', 'Mailbox not found or inactive');
        echo json_encode($response);
        exit;
    }
    
    // Get the signature
    $signature = $mailbox->signature;
    
    if (empty($signature)) {
        $response['msg'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt', 'No signature found for this mailbox');
        echo json_encode($response);
        exit;
    }
        
    $conv = new erLhcoreClassModelMailconvConversation();

    $signature = str_replace([
            '{operator}',
            '{department}',
            '{operator_chat_name}'
        ],[
        $currentUser->getUserData()->name_official,
        '',
        $currentUser->getUserData()->name_support
        ],$signature);

    $signature = erLhcoreClassGenericBotWorkflow::translateMessage($signature, array('chat' => $conv));

    // Success
    $response['error'] = false;
    $response['signature'] = $signature;
    $response['msg'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt', 'Signature fetched successfully');
    
} catch (Exception $e) {
    $response['msg'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt', 'An error occurred') . ': ' . $e->getMessage();
}

echo json_encode($response);
exit;

?>
