<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Edit language')?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('speech/editlanguage')?>/<?php echo $item->id?>" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhspeech/form/form_language.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>

<?php if ($item->id > 0) : ?>
    <hr>
    <?php $dialectsLanguage = erLhcoreClassModelSpeechLanguageDialect::getList(array('filter' => array('language_id' => $item->id))); ?>
    <div class="row">
        <?php foreach ($dialectsLanguage as $langDialect) : ?>
            <div class="col-4">
                <label class="fs12"><?php echo htmlspecialchars($langDialect->language)?> [<?php echo htmlspecialchars($langDialect->lang_code)?>]</label>
            </div>
            <?php if ($langDialect->short_code != '') : ?>
                <div class="col-4">
                    <label class="fs12"><?php echo htmlspecialchars($langDialect->language)?> [<?php echo htmlspecialchars($langDialect->short_code)?>]</label>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif;?>