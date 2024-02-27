<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>
<?php if ($parts_top_menu_chat_actions_enabled == true) :

if ($currentUser->hasAccessTo('lhchat','allowchattabs')) {
    $menuItems[] = array('href' => '#', 'onclick' => 'javascript:lhinst.chatTabsOpen()' ,'iclass' => 'chat', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs'));
} 

$menuItems[] = array('href' => erLhcoreClassDesign::baseurl('chat/list'),'iclass' => 'list', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats List'));

if ($currentUser->hasAccessTo('lhmailconv','use_admin')) {
    $menuItems[] = array('href' => erLhcoreClassDesign::baseurl('mailconv/conversations'), 'iclass' => 'mail_outline', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout', 'Mails List'));
    $menuItems[] = array('href' => erLhcoreClassDesign::baseurl('mailconv/sendemail'), 'iclass' => 'send', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout', 'New e-mail'));
}

if ($currentUser->hasAccessTo('lhviews','use')) {
    $menuItems[] = array('href' => erLhcoreClassDesign::baseurl('views/home'), 'iclass' => 'saved_search', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout', 'My views'));
}

endif;?>