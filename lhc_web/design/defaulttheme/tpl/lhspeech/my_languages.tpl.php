<?php $userLanguages = erLhcoreClassSpeech::getUserLanguages($user->id); ?>

<div>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','What languages you speak?')?></label>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','If pending chats comes in the same language as chosen one. To you will be assigned same language chats first.')?></i></small></p>
</div>

<div class="row">
<?php foreach (erLhcoreClassModelSpeechLanguage::getList(array('sort' => 'name ASC')) as $speechLanguage) : ?>
<div class="col-4">
<?php
    $dialectsLanguage = erLhcoreClassModelSpeechLanguageDialect::getList(array('filter' => array('language_id' => $speechLanguage->id)));
    $allChecked = true;
    foreach ($dialectsLanguage as $langDialect) {
        if (!key_exists($langDialect->lang_code,$userLanguages)){
            $allChecked = false;
            break;
        }
        if ($langDialect->short_code != '' && !key_exists($langDialect->short_code,$userLanguages)) {
            break;
        }
    }
?>
    <div>
        <label><input type="checkbox" <?php if ($allChecked == true) : ?>checked="checked"<?php endif?>  onchange="changeLanguage($(this))" value="<?php echo htmlspecialchars($speechLanguage->id)?>" ><?php echo htmlspecialchars($speechLanguage)?></label>
        <a onclick="$('.language-content-<?php echo htmlspecialchars($speechLanguage->id)?>').toggle()"><i class="material-icons mr-0">visibility</i> </a>
    </div>

    <div class="row language-content-<?php echo htmlspecialchars($speechLanguage->id)?>" style="display: none">
    <?php foreach ($dialectsLanguage as $langDialect) : ?>
        <div class="col-6">
            <label class="fs12">
                <input class="speech-language-<?php echo $speechLanguage->id?>" name="userLanguages[]" <?php if (key_exists($langDialect->lang_code,$userLanguages)) : ?>checked="checked"<?php endif;?> type="checkbox" value="<?php echo htmlspecialchars($langDialect->lang_code)?>" > <?php echo htmlspecialchars($langDialect->language)?> [<?php echo htmlspecialchars($langDialect->lang_code)?>]
            </label>
        </div>

        <?php if ($langDialect->short_code != '') : ?>
        <div class="col-6">
            <label class="fs12">
                <input  class="speech-language-<?php echo $speechLanguage->id?>" name="userLanguages[]" <?php if (key_exists($langDialect->short_code,$userLanguages)) : ?>checked="checked"<?php endif;?> type="checkbox" value="<?php echo htmlspecialchars($langDialect->short_code)?>" > <?php echo htmlspecialchars($langDialect->language)?> [<?php echo htmlspecialchars($langDialect->short_code)?>]
            </label>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
    <script>
        function changeLanguage(inst) {
            var identifier = '.speech-language-' + inst.val();
            if (inst.is(':checked')) {
                $(identifier).prop('checked',true);
            } else {
                $(identifier).prop('checked',false);
            };
        }
    </script>
</div>