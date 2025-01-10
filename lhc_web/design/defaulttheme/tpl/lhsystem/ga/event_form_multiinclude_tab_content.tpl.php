<?php /* Override me */ ?>
<?php /*
<div role="tabpanel" class="tab-pane <?php if ($tab == 'bot') : ?>active<?php endif;?>" id="bot">
    <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [botTrigger]" class="fw-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['botTrigger_on']) && $ga_options['botTrigger_on'] == 1) : ?>checked="checked"<?php endif;?> name="botTrigger_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Bot trigger was executed')?></label>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                <input type="text" class="form-control form-control-sm" name="botTrigger_category" value="<?php isset($ga_options['botTrigger_category']) ? print htmlspecialchars($ga_options['botTrigger_category']) : print 'Bot'?>" />
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                <input type="text" class="form-control form-control-sm" name="botTrigger_action" value="<?php isset($ga_options['botTrigger_action']) ? print htmlspecialchars($ga_options['botTrigger_action']) : print 'Trigger'?>" />
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'We will set eventLabel to trigger name')?></p>
            </div>
        </div>
    </div>
</div>*/ ?>