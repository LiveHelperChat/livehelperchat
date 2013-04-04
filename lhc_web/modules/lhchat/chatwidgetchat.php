<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chat.tpl.php');

try {

    $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($Chat->hash == $Params['user_parameters']['hash'])
    {
        $tpl->set('chat_id',$Params['user_parameters']['chat_id']);
        $tpl->set('hash',$Params['user_parameters']['hash']);
        $tpl->set('chat',$Chat);
        $tpl->set('chat_widget_mode',true);

        // User online
        $Chat->user_status = 0;
        $Chat->support_informed = 1;
        erLhcoreClassChat::getSession()->update($Chat);

    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}



$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';

$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chat started')))


?>