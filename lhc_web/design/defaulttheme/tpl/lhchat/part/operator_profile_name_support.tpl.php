<div><strong><?php echo htmlspecialchars($user->name_support)?></strong>
    <?php if (isset($extraMessage)) : ?>
        &nbsp;<i><?php echo $extraMessage;?></i>
    <?php endif;?>
</div>