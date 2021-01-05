<tr>
    <td colspan="2">

        <h6 class="font-weight-bold py-2"><i class="material-icons">query_builder</i>Times</h6>

        <div class="row text-muted">
            <div class="col-6 pb-2">
                <span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?></span> - <span id="chat-duration-<?php echo $chat->id?>"><?php echo $chat->chat_duration_front?></span>
            </div>

            <?php if (isset($orderInformation['wait_time']['enabled']) && $orderInformation['wait_time']['enabled'] == true && $chat->wait_time > 0) : ?>
            <div class="col-6 pb-2">
                 <span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?> - </span><?php echo $chat->wait_time_front?>
            </div>
            <?php endif; ?>

            <?php if (isset($orderInformation['created']['enabled']) && $orderInformation['created']['enabled'] == true) : ?>
                <div class="col-6 pb-2">
                    <span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created at')?> - </span><?php echo $chat->time_created_front?>
                </div>

                 <?php if ($chat->pnd_time != $chat->time && $chat->pnd_time > 0) : ?>
                <div class="col-6 pb-2">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Started at')?> - <?php echo $chat->pnd_time_front?>
                </div>
                <?php endif; ?>

                <?php if ($chat->cls_time > 0 && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
                <div class="col-6 pb-2">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closed at')?> - <?php echo $chat->cls_time_front?>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ( !empty($chat->user_tz_identifier) ) : ?>
            <div class="col-6 pb-2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Time zone')?>">
               <?php echo htmlspecialchars($chat->user_tz_identifier)?>, <?php echo htmlspecialchars($chat->user_tz_identifier_time)?>
            </div>
            <?php endif;?>

            <?php if ($chat->user_closed_ts > 0 && $chat->user_status == 1) : ?>
            <div class="col-6 pb-2">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','User left')?> - <?php echo $chat->user_closed_ts_front?>
            </div>
            <?php endif;?>

        </div>

    </td>
</tr>