<?php if (isset($orderInformation['referrer']['enabled']) && $orderInformation['referrer']['enabled'] == true && !empty($chat->referrer)) : ?>
<div class="col-12 pb-1">
    <a target="_blank" style="max-width: 400px;" class="text-muted text-truncate d-inline-block" rel="noopener" title="<?php echo htmlspecialchars($chat->referrer)?>" href="<?php echo htmlspecialchars($chat->referrer)?>"><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Page from where visitor started a chat.')?>" class="material-icons">link</span><?php echo htmlspecialchars($chat->referrer)?></a>
</div>
<?php endif;?>