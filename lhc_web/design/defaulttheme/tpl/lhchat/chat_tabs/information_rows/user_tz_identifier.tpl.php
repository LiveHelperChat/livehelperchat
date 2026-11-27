<?php if (isset($orderInformation['user_tz_identifier']['enabled']) && $orderInformation['user_tz_identifier']['enabled'] == true && !empty($chat->user_tz_identifier)) : ?>
<div class="col-6 pb-1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Time zone')?>">
    <?php echo htmlspecialchars($chat->user_tz_identifier)?>, <?php echo htmlspecialchars($chat->user_tz_identifier_time)?>
</div>
<?php endif;?>

