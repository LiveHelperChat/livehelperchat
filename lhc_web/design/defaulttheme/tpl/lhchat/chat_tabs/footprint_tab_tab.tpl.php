<?php if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
<li role="presentation"><a href="#footprint-tab-chat-<?php echo $chat->id?>" aria-controls="footprint-tab-chat-<?php echo $chat->id?>" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Footprint')?></a></li>
<?php endif;?>