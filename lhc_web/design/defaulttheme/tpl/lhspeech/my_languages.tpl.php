<?php $userLanguages = erLhcoreClassSpeech::getUserLanguages($user->id); ?>

<div>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','What languages you speak?')?></label>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','If pending chats comes in the same language as chosen one. To you will be assigned same language chats first.')?></i></small></p>
</div>

<?php foreach (erLhcoreClassModelSpeechLanguageDialect::getList() as $langDialect) : ?>
<div class="col-xs-3">
    <label class="fs12">
        <input name="userLanguages[]" <?php if (key_exists($langDialect->lang_code,$userLanguages)) : ?>checked="checked"<?php endif;?> type="checkbox" value="<?php echo htmlspecialchars($langDialect->lang_code)?>" > <?php echo htmlspecialchars($langDialect->language)?> [<?php echo htmlspecialchars($langDialect->lang_code)?>]
    </label>
    <?php if ($langDialect->short_code != '') : ?>
    <br/>
    <label class="fs12">
        <input name="userLanguages[]" <?php if (key_exists($langDialect->short_code,$userLanguages)) : ?>checked="checked"<?php endif;?> type="checkbox" value="<?php echo htmlspecialchars($langDialect->short_code)?>" > <?php echo htmlspecialchars($langDialect->language)?> [<?php echo htmlspecialchars($langDialect->short_code)?>]
    </label>
    <br/>
    <?php endif; ?>
</div>
<?php endforeach; ?>