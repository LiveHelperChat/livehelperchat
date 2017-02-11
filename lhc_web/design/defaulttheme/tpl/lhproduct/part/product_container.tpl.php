<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Product');?>*</label>
<select class="form-control" name="ProductID" id="ProductID_id">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Choose a product');?></option>
    <?php foreach ($products as $product) : ?>
        <option value="<?php echo $product->id?>" <?php if ($input_data->product_id == $product->id) : ?>selected="selected"<?php endif;?> ><?php echo htmlspecialchars($product->name)?></option>
    <?php endforeach;?>
</select>