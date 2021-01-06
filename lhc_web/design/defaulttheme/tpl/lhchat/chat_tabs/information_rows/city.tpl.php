<?php if (isset($orderInformation['city']['enabled']) && $orderInformation['city']['enabled'] == true && !empty($chat->city) ) : ?>
<div class="col-6 pb-1">
    <?php echo htmlspecialchars($chat->city);?>
</div>
<?php endif;?>