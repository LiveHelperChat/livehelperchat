<?php if (isset($errors)) : ?>
<div data-alert class="alert alert-danger alert-dismissible fade show p-2 ps-4" ng-non-bindable>
<?php if (!isset($hideErrorButton)) : ?>
<button type="button" class="btn-close pt-1 pe-1" data-bs-dismiss="alert" aria-label="Close">
   
</button>
<?php endif;?>
<ul class="ps-1 m-0">
<?php foreach ($errors as $err) : ?>
    <li><?php echo $err?></li>
<?php endforeach;?>
</ul>
</div>
<?php endif;?>