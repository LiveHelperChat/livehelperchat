<?php if (isset($orderInformation['user_left']['enabled']) && $orderInformation['user_left']['enabled'] == true && $chat->user_closed_ts > 0 && $chat->user_status == 1) : ?>
<div class="col-6 pb-1">
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','User left')?> - <?php echo $chat->user_closed_ts_front?>
</div>
<?php endif;?>