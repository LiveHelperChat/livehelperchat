<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab_tab_pre.tpl.php'));?>	
<?php if ($information_tab_map_tab_tab_enabled == true) : ?>
<li role="presentation"><a href="#map-tab-chat-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Map')?>" aria-controls="map-tab-chat-<?php echo $chat->id?>" role="tab" data-toggle="tab"><i class="icon-globe"></i></a></li>
<?php endif;?>