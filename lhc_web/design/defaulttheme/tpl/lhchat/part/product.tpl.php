<?php

if (erLhcoreClassModelChatConfig::fetch('product_enabled_module')->current_value == 1) : 

$filter = array('sort' => 'priority ASC, name ASC');

if (isset($input_data->product_id_array) && !empty($input_data->product_id_array)) {
    $filter['filterin']['id'] = $input_data->product_id_array;
}

if (isset($input_data->departament_id_array) && !empty($input_data->departament_id_array)) {
    $filter['filterin']['departament_id'] = $input_data->departament_id_array;
}

if ($input_data->departament_id > 0) {
    $filter['filterin']['departament_id'][] = $input_data->departament_id;
}

$filter['filter']['disabled'] = 0;

$products = erLhAbstractModelProduct::getList($filter);

if (!empty($products)) : $departmentsOptions['hide_department'] = true; ?>

<input type="hidden" name="HasProductID" value="on" />

<?php if (isset($input_data->product_id_array)) : foreach ($input_data->product_id_array as $definedProduct) : ?>
<input type="hidden" name="ProductIDDefined[]" value="<?php echo $definedProduct?>" />
<?php endforeach; endif; ?>

<div class="form-group<?php if (isset($errors['ProductID'])) : ?> has-error<?php endif;?>">
    <label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Product');?>*</label>
    <select class="form-control" name="ProductID">
            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Choose a product');?></option>
        <?php foreach ($products as $product) : ?>
            <option value="<?php echo $product->id?>" <?php if ($input_data->product_id == $product->id) : ?>selected="selected"<?php endif;?> ><?php echo htmlspecialchars($product->name)?></option>
        <?php endforeach;?>
    </select>
</div>
<?php endif; endif; ?>