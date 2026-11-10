<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control form-control-sm" maxlength="100" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group"  ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Nick, what should be operator nick. E.g Support Bot');?></label>
    <input type="text" class="form-control form-control-sm" maxlength="100" name="Nick"  value="<?php echo htmlspecialchars($item->nick);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Photo');?>, (jpg,png)</label>
    <input type="file" name="UserPhoto" value="" />
</div>

<div class="form-group">
    <label><input type="checkbox" name="use_translation_service" <?php $item->use_translation_service == 1 ? print 'checked="checked"' : ''?> value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','If translation is not found use translation service')?></label>
    <p><i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','If you have configured Automatic Translations we will use it for untranslated items.');?></small></i></p>
</div>

<?php $trConfiguration = $item->configuration_array; ?>

<div class="form-group">
    <label><input type="checkbox" name="detect_chat_language" <?php !empty($trConfiguration['detect_chat_language']) ? print 'checked="checked"' : ''?> value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Detect chat language with translation service instead of relying on chat locale')?></label>
    <p><i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Language detection is executed only when the translation service is actually used, e.g. when a translation item for a bot message is not found and the text has to be translated by the service. If a translation item is found by its identifier, the chat locale is used - to detect the language for that specific identifier instead, enable "Try detect language using AI service before translating" in the translation item itself. Detected language is stored in chat variables, so detection is executed only once per chat.');?></small></i></p>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','For automatic translations we have to know what is the main bot language. From this language we will translate bot messages.');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'bot_lang',
        'css_class'      => 'form-control form-control-sm',
        'selected_id'    => $item->bot_lang,
        'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
    )); ?>
</div>

<?php if ($item->has_photo) : ?>
    <div class="form-group">
        <img src="<?php echo $item->photo_path?>" alt="" width="50" /><br />
        <label><input type="checkbox" name="DeletePhoto" value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
    </div>
<?php endif;?>