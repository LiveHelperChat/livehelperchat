<?php if (isset($orderInformation['city']['enabled']) && $orderInformation['city']['enabled'] == true && !empty($chat->city) ) : $partsCity = explode('||',$chat->city)?>
<div class="col-6 pb-1">
    <span class="material-icons">location_city</span><?php echo htmlspecialchars(trim($partsCity[0]));?>
</div>

<?php if (isset($partsCity[1])) : ?>
<div class="col-6 pb-1">
    <span class="material-icons">map</span><?php echo htmlspecialchars(trim($partsCity[1]));?>
</div>
<?php endif; ?>

<?php endif;?>