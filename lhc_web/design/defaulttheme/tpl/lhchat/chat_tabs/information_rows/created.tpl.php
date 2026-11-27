<?php if (isset($orderInformation['created']['enabled']) && $orderInformation['created']['enabled'] == true) : ?>
    <div class="col-6 pb-1">
        <span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created at')?> - </span><?php echo $chat->time_created_front?>
    </div>

    <?php if ($chat->pnd_time != $chat->time && $chat->pnd_time > 0) : ?>
        <div class="col-6 pb-1">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Started wait at')?> - <?php echo $chat->pnd_time_front?>
        </div>
    <?php endif; ?>

    <?php if ($chat->cls_time > 0 && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
        <div class="col-6 pb-1">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closed at')?> - <?php echo $chat->cls_time_front?>
        </div>
    <?php endif; ?>

<?php endif; ?>