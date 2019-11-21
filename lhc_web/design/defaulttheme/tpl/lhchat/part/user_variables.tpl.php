<?php if (isset($input_data->jsvar)) : foreach ($input_data->jsvar as $index => $item) : ?>
    <input type="hidden" name="jsvar[<?php echo $index?>]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;endif;?>

<?php if (isset($input_data->tag) && !empty($input_data->tag)) : $tag = implode(',',array_unique(explode(',',$input_data->tag))); ?>
    <input type="hidden" name="tag" value="<?php echo htmlspecialchars($tag)?>" />
<?php endif; ?>

<?php if (isset($input_data->bot_id) && is_numeric($input_data->bot_id)) : ; ?>
    <input type="hidden" name="bot_id" value="<?php echo htmlspecialchars($input_data->bot_id)?>" />
<?php endif; ?>

<?php
$modeUserVariables = isset($modeUserVariables) ? $modeUserVariables : 'on';

if (!empty($input_data->name_items)) {
	$hasExtraField = true;
};

foreach ($input_data->name_items as $item) : ?>
	<input type="hidden" name="name_items[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;?>

<?php if (isset($input_data->value_sizes)) : foreach ($input_data->value_sizes as $item) : ?>
	<input type="hidden" name="value_sizes[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;endif;?>

<?php if (isset($input_data->values_req)) : foreach ($input_data->values_req as $item) : ?>
	<input type="hidden" name="values_req[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;endif;?>

<?php if (isset($input_data->value_show)) : foreach ($input_data->value_show as $item) : ?>
	<input type="hidden" name="value_show[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;endif;?>

<?php if (isset($input_data->hattr)) : foreach ($input_data->hattr as $item) : ?>
	<input type="hidden" name="hattr[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;endif;?>

<?php if (isset($input_data->encattr)) : foreach ($input_data->encattr as $item) : ?>
	<input type="hidden" name="encattr[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;endif;?>

<?php
$hasVisibleField = false;
foreach ($input_data->value_types as $key => $item) : 
$showField = ($input_data->value_show[$key] == $modeUserVariables || $input_data->value_show[$key] == 'b');
($hasVisibleField = ($item == 'text' && $showField == true) ? true : $hasVisibleField); ?>
<input type="hidden" name="value_types[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;?>

<?php if ($hasVisibleField == true) : ?><div class="row"><?php endif;?>
<?php foreach ($input_data->value_items as $key => $item) : 

$showField = ((isset($input_data->value_show[$key]) && $input_data->value_show[$key] == $modeUserVariables) || (isset($input_data->value_show[$key]) && $input_data->value_show[$key] == 'b'));

$visibleItem = (isset($input_data->value_types[$key]) && $input_data->value_types[$key] == 'text' && isset($input_data->name_items[$key])); ?>

<?php if ($visibleItem == true && $showField == true) : ?>
<div class="form-group col-<?php isset($input_data->value_sizes[$key]) ? print (int)$input_data->value_sizes[$key] : print 6?>"><label class="col-form-label"><?php echo htmlspecialchars($input_data->name_items[$key])?><?php isset($input_data->values_req[$key]) && $input_data->values_req[$key] == 't' ? print '*' : ''?></label>
<?php endif;?>

<input class="form-control form-control-sm<?php if (isset($errors['additional_'.$key])) : ?> is-invalid<?php endif;?>" type="<?php isset($input_data->value_types[$key]) && $showField == true ? print htmlspecialchars($input_data->value_types[$key]) : print 'hidden' ?>" name="value_items[]" value="<?php echo htmlspecialchars($item)?>" />


<?php if ($visibleItem == true && $showField == true) : ?></div><?php endif;?>
<?php endforeach;?>
<?php if ($hasVisibleField == true) : ?></div><?php endif;?>