<?php if (isset($errors)) : ?>
<div data-alert class="alert alert-danger alert-dismissible fade show">
<?php if (!isset($hideErrorButton)) : ?>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
<?php endif;?>
<ul class="pl-1 m-0">
<?php foreach ($errors as $err) : ?>
    <li><?php echo $err?></li>
<?php endforeach;?>
</ul>
</div>
<?php endif;?>