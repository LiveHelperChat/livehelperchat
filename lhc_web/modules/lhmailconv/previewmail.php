<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/previewmail.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvConversation)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
    if (isset($mailData['mail'])) {
        $mail = $mailData['mail'];
    }
}

if (is_object($mail) && erLhcoreClassChat::hasAccessToRead($mail) )
{
    $keyword = isset($_GET['keyword']) ? (string)$_GET['keyword'] : '';
    $keyword = str_replace(":",': ',$keyword);
    preg_match_all('~(?|"([^"]+)"|(\S+))~', $keyword, $matches);
    $keywords = [];
    if (isset($matches[1]) && !empty($matches[1])){
        foreach ($matches[1] as $potentionalKeyword) {
            if (trim(str_ireplace(['and','or'],'',$potentionalKeyword)) != '' && str_ends_with($potentionalKeyword,":") === false) {
                $keywords[] = $potentionalKeyword;
            }
        }
    }

    $tpl->set('keyword',$keywords);
    $tpl->set('chat',$mail);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>