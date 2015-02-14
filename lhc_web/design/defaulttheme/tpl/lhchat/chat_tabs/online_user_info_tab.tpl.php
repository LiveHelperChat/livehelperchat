<?php if ( ($online_user = $chat->online_user) !== false) : ?>
<li role="presentation"><a href="#online-user-info-tab-<?php echo $chat->id?>" aria-controls="online-user-info-tab-<?php echo $chat->id?>" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Browsing')?></a></li>
<?php endif;?>