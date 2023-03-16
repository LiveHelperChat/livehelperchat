<?php if (isset($orderInformation['phone']['enabled']) && $orderInformation['phone']['enabled'] == true && !empty($chat->phone)) : ?>
    <div class="col-6 pb-1">
        <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Phone')?>">phone</span>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','use_unhidden_phone')) : ?>
            <?php echo htmlspecialchars($chat->phone)?>
        <?php else : ?>
            <?php echo htmlspecialchars(LiveHelperChat\Helpers\Anonymizer::maskPhone($chat->phone))?>
        <?php endif; ?>
    </div>
<?php endif;?>