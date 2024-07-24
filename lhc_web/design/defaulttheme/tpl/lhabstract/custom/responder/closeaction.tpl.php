<div class="row">
    <div class="col-9">
        <div class="form-group">
            <label><?php echo $fields['close_message']['trans'];?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a></label>
            <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_close_message]'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
            <?php echo erLhcoreClassAbstract::renderInput('close_message', $fields['close_message'], $object)?>
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Message will be sent only if chat is in active or bot status.');?></p>
        </div>
    </div>
</div>