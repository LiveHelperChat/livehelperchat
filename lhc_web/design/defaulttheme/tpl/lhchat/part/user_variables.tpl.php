<?php foreach ($input_data->name_items as $item) : ?>
	<input type="hidden" name="name_items[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;?>

<?php foreach ($input_data->value_items as $item) : ?>
	<input type="hidden" name="value_items[]" value="<?php echo htmlspecialchars($item)?>" />
<?php endforeach;?>
