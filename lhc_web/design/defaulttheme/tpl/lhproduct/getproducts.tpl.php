<?php if (!empty($items)) : ?>
<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Product');?><?php if ($required == true) : ?>*<?php endif;?></label>
<select class="form-control" name="ProductID" id="ProductID_id">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Choose a product');?></option>
        <?php foreach ($items as $item) : ?>
            <option value="<?php echo $item->product_id?>" <?php echo $product_id == $item->product_id ? ' selected="selected" ' : ''?>><?php echo htmlspecialchars($item->product_name)?></option>
        <?php endforeach;?>
</select>
<?php endif;?>