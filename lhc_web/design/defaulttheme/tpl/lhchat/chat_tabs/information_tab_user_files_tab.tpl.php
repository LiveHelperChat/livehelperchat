<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files_tab_pre.tpl.php'));?>	
<?php if ($information_tab_user_files_tab_enabled == true) : ?>
<li role="presentation"><a href="#main-user-info-files-<?php echo $chat->id?>" aria-controls="main-user-info-files-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Files')?>" role="tab" data-toggle="tab"><i class="icon-attach"></i></a></li>
<?php endif;?>