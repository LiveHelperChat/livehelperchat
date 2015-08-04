<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_online_users_pre.tpl.php'));?>
<?php if ($parts_top_menu_online_users_enabled == true && $currentUser->hasAccessTo('lhchat','use_onlineusers')) : ?>
<?php $menuItems[] = array('href' => erLhcoreClassDesign::baseurl('chat/onlineusers'),'iclass' => 'face', 'text' => erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online visitors')); ?>
<?php endif;?>	