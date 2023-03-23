<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','use_admin') && erLhcoreClassModelMailconvMailbox::getCount(['filter' => ['mail' => $chat->department->email]]) == 1) : ?>
<div class="col-6 pb-1">
    <a class="text-muted <?php if ($chat->mail_send == 1) : ?>text-success<?php endif; ?>" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>/(chat_id)/<?php echo $chat->id?>"><span class="material-icons">mail</span> <?php if ($chat->mail_send == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mail was send')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?><?php endif;?></a>
</div>
<?php elseif (erLhcoreClassUser::instance()->hasAccessTo('lhchat','sendmail') && (!isset($chat->department->bot_configuration_array['hide_send_email']) || $chat->department->bot_configuration_array['hide_send_email'] == false)) : ?>
<div class="col-6 pb-1">
    <?php
    $dataMailConfig = erLhcoreClassModelChatConfig::fetch('mailconv_options_general')->data;
    if (isset($dataMailConfig['mail_module_as_send']) && $dataMailConfig['mail_module_as_send'] == true) : ?>
        <a class="text-muted <?php if ($chat->mail_send == 1) : ?>text-success<?php endif; ?>" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>/(chat_id)/<?php echo $chat->id?>"><span class="material-icons">mail</span> <?php if ($chat->mail_send == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mail was send')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?><?php endif;?></a>
    <?php else : ?>
        <a class="text-muted <?php if ($chat->mail_send == 1) : ?>text-success<?php endif; ?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/sendmail/<?php echo $chat->id?>'})"><span class="material-icons">mail</span> <?php if ($chat->mail_send == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mail was send')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?><?php endif;?></a>
    <?php endif; ?>
</div>
<?php endif; ?>