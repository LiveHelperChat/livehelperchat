<?php if ($chat->gbot_id > 0) : ?>
<div class="col-6 pb-1">
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhgenericbot','see_actions')) : ?>
    <a class="text-muted" onclick="return lhc.revealModal({'title' : 'Bot involved', 'mparams':{'backdrop':false},'url':WWW_DIR_JAVASCRIPT +'genericbot/chatactions/<?php echo $chat->id?>/'})">
    <?php endif; ?>
    <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Bot ID')?> - <?php echo $chat->gbot_id?>"><i class="material-icons">android</i>
    <?php if ($chat->bot && !empty($chat->bot->short_name)) : ?>
        <?php echo htmlspecialchars($chat->bot->short_name); ?>
    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Bot')?>
    <?php endif;?>
</span>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhgenericbot','see_actions')) : ?>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>