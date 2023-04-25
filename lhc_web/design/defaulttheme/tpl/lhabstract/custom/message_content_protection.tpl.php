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

<div class="form-group">
    <label><?php echo $fields['enabled']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('enabled', $fields['enabled'], $object)?>
</div>

<?php /*
<div class="form-group">
    <label><?php echo $fields['remove']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('remove', $fields['remove'], $object)?>
</div>*/ ?>

<div class="form-group">
    <div class="pb-1">
        <label class="pe-1"><?php echo $fields['pattern']['trans'];?></label><button class="btn btn-xs btn-secondary me-1 protect-button" data-protect="__email__|||$|||*" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Add an e-mail masking');?></button><button  data-protect="__credit_card__|||*" class="protect-button btn btn-xs btn-secondary me-1" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Add a credit card masking');?></button><button class="protect-button btn btn-xs btn-secondary me-1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Replaces all numbers in the message');?>" data-protect="(\d+)|||_" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Add a preg match sample');?></button>
    </div>

    <?php echo erLhcoreClassAbstract::renderInput('pattern', $fields['pattern'], $object)?>
    <p class="fs14"><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','New rule per row.');?> <span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Rule ||| Replace symbol');?></span></i></small></p>
</div>

<div class="form-group">
    <div class="pb-1">
    <label class="pe-1"><?php echo $fields['v_warning']['trans'];?></label><button class="btn btn-xs btn-secondary me-1" id="sample-button" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Sample');?></button>
    </div>
    <?php echo erLhcoreClassAbstract::renderInput('v_warning', $fields['v_warning'], $object)?>
</div>

<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-sm btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-sm btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-sm btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    <input type="button" id="test-protect-rules" class="btn btn-sm btn-outline-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Test masking rules');?>"/>
</div>

<script>
    $('.protect-button').click(function(){
        var txt = $("textarea[name='AbstractInput_pattern']");
        txt.val((txt.val() != "" ? txt.val() + "\n" : '') + $(this).attr('data-protect'));
    });
    $('#test-protect-rules').click(function(){
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'abstract/testmasking/','loadmethod':'post',datapost:{mask:$("textarea[name='AbstractInput_pattern']").val()}});
    });
    $('#sample-button').click(function (){
        $("textarea[name='AbstractInput_v_warning']").val('[html]<div class="fs14 text-danger fw-bold fst-italic">For your protection, we ask that you do not share full credit numbers unless speaking directly with a processing agent.</div>[/html]');
    });
</script>

<?php include(erLhcoreClassDesign::designtpl('lhabstract/parts/after_form.tpl.php'));?>