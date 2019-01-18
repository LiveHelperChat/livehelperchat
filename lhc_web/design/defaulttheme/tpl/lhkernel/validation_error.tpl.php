<?php if (isset($errors)) : ?>
<div data-alert class="alert alert-danger alert-dismissible fade in">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
<ul class="validation-ul">
<?php foreach ($errors as $err) : ?>
    <li><?php echo $err?></li>
<?php endforeach;?>
</ul>
</div>
<?php endif;?>