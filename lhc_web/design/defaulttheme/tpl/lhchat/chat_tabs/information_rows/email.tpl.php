<?php if (isset($orderInformation['email']['enabled']) && $orderInformation['email']['enabled'] == true && !empty($chat->email)) : ?>
    <div class="col-6 pb-1">
        <span class="material-icons">email</span><a class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?>" href="mailto:<?php echo $chat->email?>"><?php echo $chat->email?></a>
    </div>
<?php endif;?>