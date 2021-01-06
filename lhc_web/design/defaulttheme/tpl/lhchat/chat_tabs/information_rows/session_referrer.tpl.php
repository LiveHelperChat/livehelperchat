<?php if (isset($orderInformation['session_referrer']['enabled']) && $orderInformation['session_referrer']['enabled'] == true && !empty($chat->session_referrer)) : ?>
    <div class="col-12 pb-1">
        <div title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?>"><span class="material-icons">flight_land</span><?php echo $chat->session_referrer != '' ? '<a target="_blank" class="text-muted" rel="noopener" title="' . htmlspecialchars($chat->session_referrer) . '" href="' . htmlspecialchars($chat->session_referrer) . '">'.htmlspecialchars($chat->session_referrer).'</a>' : ''?></div>
    </div>
<?php endif;?>