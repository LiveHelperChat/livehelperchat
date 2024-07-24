<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','If chat was accepted by the same language speaking operator you can send visitor a custom message on chat accept event.')?></p>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','What languages should be ignored. If chat language is one of the selected, message will not be send.')?></label>
    <lhc-multilanguage-tab-content language_field_name="languages_ignore[]" tab_class="no-tab"  identifier="autoResponderIgnore" <?php if ($object->languages != '') : ?>init_langauges="<?php echo ($object->id > 0 ? $object->id : 0)?>"<?php endif;?>></lhc-multilanguage-tab-content>
</div>

<div class="form-group">
    <label><?php echo $fields['multilanguage_message']['trans'];?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','If you leave empty - message we will be send only if translated message is found.')?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a></label>
    <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_multilanguage_message]'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
    <?php echo erLhcoreClassAbstract::renderInput('multilanguage_message', $fields['multilanguage_message'], $object)?>
</div>