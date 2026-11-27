<?php if (isset($orderInformation['wait_time']['enabled']) && $orderInformation['wait_time']['enabled'] == true && $chat->wait_time > 0) : ?>
<div class="col-6 pb-1">
    <span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?> - </span><?php echo $chat->wait_time_front?>
</div>
<?php endif; ?>