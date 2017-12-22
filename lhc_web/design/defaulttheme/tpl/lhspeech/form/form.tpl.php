<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Language')?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
        'input_name' => 'language_id',
        'selected_id' => $item->language_id,
        'display_name' => 'name',
        'css_class' => 'form-control',
        'list_function' => 'erLhcoreClassModelSpeechLanguage::getList',
        'list_function_params' => array()
    )); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Dialect name')?></label>
    <input type="text" class="form-control" name="lang_name" value="<?php echo htmlspecialchars($item->lang_name)?>">
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Language code')?></label>
    <input type="text" class="form-control" name="lang_code" value="<?php echo htmlspecialchars($item->lang_code)?>">
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Short code')?></label>
    <input type="text" class="form-control" name="short_code" value="<?php echo htmlspecialchars($item->short_code)?>">
</div>