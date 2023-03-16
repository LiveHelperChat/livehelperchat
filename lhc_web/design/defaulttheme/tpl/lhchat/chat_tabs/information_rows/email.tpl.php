<?php if (
        isset($orderInformation['email']['enabled']) && $orderInformation['email']['enabled'] == true && !empty($chat->email) &&
        erLhcoreClassUser::instance()->hasAccessTo('lhchat','chat_see_email')
) : ?>
    <div class="col-6 pb-1">
        <span class="material-icons">email</span>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','chat_see_unhidden_email')) : ?>
            <a class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?>" href="mailto:<?php echo $chat->email?>"><?php echo htmlspecialchars($chat->email)?></a>
        <?php else : ?>
            <?php echo htmlspecialchars(LiveHelperChat\Helpers\Anonymizer::maskEmail($chat->email))?>
        <?php endif; ?>
    </div>
<?php endif;?>