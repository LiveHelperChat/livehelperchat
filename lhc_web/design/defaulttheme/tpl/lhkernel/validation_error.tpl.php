<?php if (isset($errors)) : ?>
<div data-alert class="alert alert-danger alert-dismissible fade show" ng-non-bindable>
<?php if (!isset($hideErrorButton)) : ?>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
   
</button>
<?php endif;?>
<ul class="ps-1 m-0">
<?php foreach ($errors as $err) : ?>
    <li><?php echo $err?></li>
<?php endforeach;?>
</ul>
</div>
<?php endif;?>