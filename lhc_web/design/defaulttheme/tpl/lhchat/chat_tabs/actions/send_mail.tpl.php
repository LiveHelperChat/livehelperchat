<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','sendmail') && (!isset($chat->department->bot_configuration_array['hide_send_email']) || $chat->department->bot_configuration_array['hide_send_email'] == false)) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" class="<?php if ($chat->mail_send == 1) : ?>text-success<?php endif; ?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/sendmail/<?php echo $chat->id?>'})"><span class="material-icons">mail</span> <?php if ($chat->mail_send == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mail was send')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?><?php endif;?></a>
</div>
<?php endif; ?>