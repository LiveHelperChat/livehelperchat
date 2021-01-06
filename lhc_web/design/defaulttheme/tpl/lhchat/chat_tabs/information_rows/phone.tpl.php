<?php if (isset($orderInformation['phone']['enabled']) && $orderInformation['phone']['enabled'] == true && !empty($chat->phone)) : ?>
    <div class="col-6 pb-1">
        <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Phone')?>">phone</span><?php echo htmlspecialchars($chat->phone)?>
    </div>
<?php endif;?>