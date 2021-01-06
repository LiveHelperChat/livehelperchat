<?php if (isset($orderInformation['referrer']['enabled']) && $orderInformation['referrer']['enabled'] == true && !empty($chat->referrer)) : ?>
<div class="col-12 pb-1">
    <div title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Page')?>"><span class="material-icons">link</span><?php echo $chat->referrer != '' ? '<a target="_blank" class="text-muted" rel="noopener" title="' . htmlspecialchars($chat->referrer) . '" href="' .htmlspecialchars($chat->referrer). '">'.htmlspecialchars($chat->referrer).'</a>' : ''?></div>
</div>
<?php endif;?>