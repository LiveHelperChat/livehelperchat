<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('ignore_pa_chat', $fields['ignore_pa_chat'], $object)?> <?php echo $fields['ignore_pa_chat']['trans'];?></label>
</div>

<div class="form-group">
<label><?php echo $fields['repeat_number']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('repeat_number', $fields['repeat_number'], $object)?>
</div>

<div class="row">
    <div class="col-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout']['trans'];?> [1]</label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout', $fields['wait_timeout'], $object)?>
        </div>
    </div>
    <div class="col-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_message']['trans'];?> [1]</label>
        <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_timeout_message]'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message', $fields['timeout_message'], $object)?>
        </div>
    </div>
    <?php if (!isset($autoResponderOptions['hide_pending_bot']) || $autoResponderOptions['hide_pending_bot'] === false) : ?>
        <div class="col-9">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_bot_id_1']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('pending_op_bot_id_1', $fields['pending_op_bot_id_1'], $object)?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_1_trigger_id']['trans'];?></label>
                        <div id="pending_op_1-trigger-list-id"></div>
                    </div>
                </div>
                <div class="col-4">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
                    <div id="pending_op_1-trigger-preview-window">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php for ($i = 2; $i <= 5; $i++) : ?>
<div class="row">
    <div class="col-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout_' . $i]['trans'];?> [<?php echo $i?>]</label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout_' . $i, $fields['wait_timeout_' . $i], $object)?>
        </div>
    </div>
    <div class="col-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_message_' . $i]['trans'];?> [<?php echo $i?>]</label>
        <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_timeout_message_'.$i.']'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message_' . $i, $fields['timeout_message_' . $i], $object)?>
        </div>
    </div>
    <?php if (!isset($autoResponderOptions['hide_pending_bot']) || $autoResponderOptions['hide_pending_bot'] === false) : ?>
        <div class="col-9">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_bot_id_' . $i]['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('pending_op_bot_id_' . $i, $fields['pending_op_bot_id_' . $i], $object)?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_' . $i . '_trigger_id']['trans'];?></label>
                        <div id="pending_op_<?php echo $i ?>-trigger-list-id"></div>
                    </div>
                </div>
                <div class="col-4">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
                    <div id="pending_op_<?php echo $i ?>-trigger-preview-window">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endfor;?>