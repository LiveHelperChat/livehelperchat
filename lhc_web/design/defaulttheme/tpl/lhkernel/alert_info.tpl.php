<div role="alert" class="alert alert-info alert-dismissible fade show" ng-non-bindable>
<?php if (!isset($hide_close_icon) || $hide_close_icon == false) : ?>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    
</button>
<?php endif;?>
<?php echo $msg?>
</div>

