<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/previewchat.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {

    $keyword = isset($_GET['keyword']) ? (string)$_GET['keyword'] : '';
    preg_match_all('~(?|"([^"]+)"|(\S+))~', $keyword, $matches);
    $keywords = [];
    if (isset($matches[1]) && !empty($matches[1])){
        foreach ($matches[1] as $potentionalKeyword) {
            if (trim(str_ireplace(['and','or'],'',$potentionalKeyword)) != '') {
                $keywords[] = $potentionalKeyword;
            }
        }
    }
    $tpl->set('keyword',$keywords);
    $tpl->set('chat',$chat);
    $tpl->set('see_sensitive_information', $currentUser->hasAccessTo('lhchat','see_sensitive_information'));
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $Result['content'] =  $tpl->fetch();
    $Result['modal_header_title'] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'No permission');
    $Result['pagelayout'] = 'modal';
}

?>