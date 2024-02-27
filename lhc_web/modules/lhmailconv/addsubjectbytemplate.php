<?php

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['message_id']);
$template = erLhcoreClassModelMailconvResponseTemplate::fetch($Params['user_parameters']['template_id']);

foreach ($template->subjects as $subject) {

    $subjectChat = erLhcoreClassModelMailconvMessageSubject::findOne(array('filter' => array('message_id' => $message->id, 'subject_id' => $subject->id)));

    if (!($subjectChat instanceof erLhcoreClassModelMailconvMessageSubject)) {
        $subjectChat = new erLhcoreClassModelMailconvMessageSubject();
    }

    $subjectChat->message_id = $message->id;
    $subjectChat->conversation_id = $message->conversation_id;
    $subjectChat->subject_id = (int)$subject->id;
    $subjectChat->saveThis();
}

exit;

?>