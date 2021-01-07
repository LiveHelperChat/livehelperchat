<?php if (isset($orderInformation['session_referrer']['enabled']) && $orderInformation['session_referrer']['enabled'] == true && !empty($chat->session_referrer)) : ?>
<div class="col-12 pb-1">
    <a target="_blank" style="max-width: 400px;" class="text-muted text-truncate d-inline-block" rel="noopener" title="<?php echo htmlspecialchars($chat->session_referrer) ?>" href="<?php echo htmlspecialchars($chat->session_referrer)?>"><span class="material-icons">flight_land</span><?php echo htmlspecialchars($chat->session_referrer)?></a>
</div>
<?php endif;?>