<?php
/**
 *
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

/*erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_reply',array(
    'mail' => & $message,
    'conversation' => & $conversation
));*/

/*$conversations = erLhcoreClassModelMailconvConversation::fetch(143);*/

foreach (erLhcoreClassModelMailconvMessage::getList(['limit' => false]) as $msg){
    \LiveHelperChat\mailConv\workers\LangWorker::detectLanguage($msg);
}

/*$message = erLhcoreClassModelMailconvMessage::fetch(153);



*/

/*erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started',array(
    'mail' => & $message,
    'conversation' => & $conversations
));*/




/*$mailbox = erLhcoreClassModelMailconvMailbox::fetch(309);

var_dump($mailbox->relevant_mailbox_id);*/

/*$conversation = erLhcoreClassModelMailconvConversation::fetch(976);

erLhcoreClassMailconvWorkflow::changePersonalMailbox($conversation,4);*/


/*
$message = erLhcoreClassModelMailconvMessage::fetch(221);
$conversation = erLhcoreClassModelMailconvConversation::fetch(189);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started',array(
    'mail' => & $message,
    'conversation' => & $conversation
));*/
/*include 'lib/vendor/autoload.php';

$mailboxHandler = new PhpImap\Mailbox(
    'host', // IMAP server incl. flags and optional mailbox folder
    'mail', // Username for the before configured mailbox
    'pass',
    false
);

$mail = $mailboxHandler->getMail(23295, false);
echo erLhcoreClassMailconvEncoding::toUTF8($mail->textPlain),"\n";

$mail = $mailboxHandler->getMail(23325, false);
echo erLhcoreClassMailconvEncoding::toUTF8($mail->textPlain),"\n";*/

//echo mb_convert_encoding($mail->textPlain,'UTF-8','ISO-8859-1');


// php72 cron.php -s site_admin -c cron/test
/*$mailbox = erLhcoreClassModelMailconvMailbox::fetch(4);
erLhcoreClassMailconvParser::syncMailbox($mailbox, ['live' => true]);*/
;
/*$mailbox = erLhcoreClassModelMailconvMailbox::fetch(3);

$filteredMatchingRules = array();
$matchingRulesByMailbox = erLhcoreClassModelMailconvMatchRule::getList(['filter' => ['active' => 1]]);
foreach ($matchingRulesByMailbox as $matchingRule) {
    if (in_array($mailbox->id,$matchingRule->mailbox_ids)) {
        $filteredMatchingRules[] = $matchingRule;
    }
}

foreach ($filteredMatchingRules as $rule){
    echo $rule->id,"\n";
}*/

/*$filteredMatchingRules = [erLhcoreClassModelMailconvMatchRule::fetch(1152)];*/

//$rule = erLhcoreClassMailconvParser::getMatchingRuleByMessage(erLhcoreClassModelMailconvMessage::fetch(1152), $filteredMatchingRules);

//var_dump($rule);

/*
$chat = erLhcoreClassModelChat::fetch(	1647599587);
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.close',array('chat' => & $chat));*/


?>