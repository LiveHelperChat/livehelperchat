<?php if (!empty($items)) : ?>
<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Product');?><?php if ($required == true) : ?>*<?php endif;?></label>
<select class="form-control" name="ProductID" id="ProductID_id">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Choose a product');?></option>
        <?php foreach ($items as $item) : ?>
            <option value="<?php echo $item->id?>" <?php echo $product_id == $item->id ? ' selected="selected" ' : ''?>><?php echo htmlspecialchars($item->name)?></option>
        <?php endforeach;?>
</select>
<?php endif;?>