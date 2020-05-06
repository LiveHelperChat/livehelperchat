<?php if (isset($metaMessage['payload']) && $metaMessage['payload'] != '') : ?>
<br/>
<div class="embed-responsive embed-responsive-16by9">
    <?php if (strpos($metaMessage['payload'],'<iframe') !== -1) : ?>
        <?php echo $metaMessage['payload']?>
    <?php else : ?>
        <video class="embed-responsive-item" <?php if (isset($metaMessage['video_options']['autoplay']) && $metaMessage['video_options']['autoplay'] == true) : ?>autoPlay<?php endif;?> <?php if (isset($metaMessage['video_options']['controls']) && $metaMessage['video_options']['controls'] == true) : ?>controls<?php endif;?>><source src="<?php echo htmlspecialchars($metaMessage['payload'])?>" /></video>
    <?php endif; ?>
</div>
<?php endif; ?>
