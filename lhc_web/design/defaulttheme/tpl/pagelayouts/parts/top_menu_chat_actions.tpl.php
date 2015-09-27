<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>
<?php if ($parts_top_menu_chat_actions_enabled == true) :

if ($currentUser->hasAccessTo('lhchat','allowchattabs')) {
    $menuItems[] = array('href' => 'javascript:void(0)', 'onclick' => 'javascript:lhinst.chatTabsOpen()' ,'iclass' => 'chat', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs')); 		
} 

$menuItems[] = array('href' => erLhcoreClassDesign::baseurl('chat/list'),'iclass' => 'list', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats list'));

endif;?>