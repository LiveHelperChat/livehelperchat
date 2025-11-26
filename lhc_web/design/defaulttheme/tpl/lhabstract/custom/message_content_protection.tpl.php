<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php
$fields = $object->getFields();
$object->languages_ignore; // Just to init
?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['name']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
        </div>
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('enabled', $fields['enabled'], $object)?> <?php echo $fields['enabled']['trans'];?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['rule_type']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('rule_type', $fields['rule_type'], $object)?>
        </div>
    </div>
</div>



<div class="d-none">
    <?php echo erLhcoreClassAbstract::renderInput('dep_ids', $fields['dep_ids'], $object)?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','If no department is chosen this will apply to all departments');?></label>
    <?php $selectedDeps = []; $depIds = (array)json_decode($object->dep_ids, true); if (!empty($depIds)) { erLhcoreClassChat::validateFilterIn($depIds); foreach (erLhcoreClassModelDepartament::getList(['limit' => false, 'filterin' => ['id' => $depIds]]) as $dep) { $selectedDeps[] = ['id' => $dep->id, 'name' => $dep->name]; } } ?>
    <lhc-department-picker json_input="AbstractInput_dep_ids" input_name="department_ids[]" selected_ids='<?php echo htmlspecialchars(json_encode($selectedDeps), ENT_QUOTES, 'UTF-8'); ?>' ajax_url="chat/searchprovider/deps" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Choose department');?>"></lhc-department-picker>
</div>

<div class="accordion pb-2" id="accordionMsgProtection">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingPattern">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePattern" aria-expanded="false" aria-controls="collapsePattern">
                <?php echo $fields['pattern']['trans'];?>
            </button>
        </h2>
        <div id="collapsePattern" class="accordion-collapse collapse" aria-labelledby="headingPattern" data-bs-parent="#accordionMsgProtection">
            <div class="accordion-body">
                <div class="form-group" ng-non-bindable>
                    <div class="d-none">
                        <?php echo erLhcoreClassAbstract::renderInput('pattern', $fields['pattern'], $object)?>
                    </div>
                    <lhc-masking-rules pii_options='<?php echo json_encode(\LiveHelperChat\Validators\Guardrails\PII::$PII_NAME_MAP); ?>' input_selector='textarea[name="AbstractInput_pattern"]'></lhc-masking-rules>
                </div>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingWarning">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWarning" aria-expanded="false" aria-controls="collapseWarning">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Auto reply warning to visitor/operator');?>
            </button>
        </h2>
        <div id="collapseWarning" class="accordion-collapse collapse" aria-labelledby="headingWarning" data-bs-parent="#accordionMsgProtection">
            <div class="accordion-body">
                <div class="form-group">
                    <div class="pb-1">
                    <label class="pe-1"><?php echo $fields['v_warning']['trans'];?></label><button class="btn btn-xs btn-secondary me-1" id="sample-button" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Sample');?></button>
                    </div>
                    <?php echo erLhcoreClassAbstract::renderInput('v_warning', $fields['v_warning'], $object)?>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-sm btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-sm btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-sm btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    <input type="button" id="test-protect-rules" class="btn btn-sm btn-outline-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Test masking rules');?>"/>
</div>

<script>
    $('#test-protect-rules').click(function(){
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'abstract/testmasking/','loadmethod':'post',datapost:{mask:$("textarea[name='AbstractInput_pattern']").val()}});
    });
    $('#sample-button').click(function (){
        $("textarea[name='AbstractInput_v_warning']").val('[html]<div class="fs14 text-danger fw-bold fst-italic">For your protection, we ask that you do not share full credit numbers unless speaking directly with a processing agent.</div>[/html]');
    });
</script>

<?php include(erLhcoreClassDesign::designtpl('lhabstract/parts/after_form.tpl.php'));?>