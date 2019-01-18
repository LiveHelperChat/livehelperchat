<?php

if (erLhcoreClassModelChatConfig::fetch('product_enabled_module')->current_value == 1) : 

if (!empty($products) || erLhcoreClassModelChatConfig::fetch('product_show_departament')->current_value == 1) : ?>

<input type="hidden" name="HasProductID" value="on" />

<?php if (isset($input_data->product_id_array)) : foreach ($input_data->product_id_array as $definedProduct) : ?>
<input type="hidden" name="ProductIDDefined[]" value="<?php echo $definedProduct?>" />
<?php endforeach; endif; ?>

<div class="form-group<?php if (isset($errors['ProductID'])) : ?> has-error<?php endif;?>" id="ProductContainer">
    <?php if (erLhcoreClassModelChatConfig::fetch('product_show_departament')->current_value == 0) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhproduct/part/product_container.tpl.php'));?>
    <?php endif;?>
</div>

<?php endif; endif; ?>