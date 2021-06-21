<?php if (isset($orderInformation['city']['enabled']) && $orderInformation['city']['enabled'] == true && !empty($chat->city) ) : ?>
<div class="col-6 pb-1">
    <span class="material-icons">location_city</span><?php echo htmlspecialchars($chat->city);?>
</div>
<?php endif;?>