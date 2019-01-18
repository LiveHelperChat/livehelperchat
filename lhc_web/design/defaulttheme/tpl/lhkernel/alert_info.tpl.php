<div role="alert" class="alert alert-info alert-dismissible fade in">
<?php if (!isset($hide_close_icon) || $hide_close_icon == false) : ?>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
<?php endif;?>
<?php echo $msg?>
</div>

