<div class="row">
<?php for ($ai = 0; $ai < 10; $ai++) : ?>
    <div class="col-6">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attribute key')?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_key_' . ($ai + 1), $fields['attrf_key_' . ($ai + 1)], $object)?>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo $ai + 1;?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attribute value')?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('attrf_val_' . ($ai + 1), $fields['attrf_val_' . ($ai + 1)], $object)?>
                </div>
            </div>
        </div>
    </div>
<?php endfor; ?>
</div>