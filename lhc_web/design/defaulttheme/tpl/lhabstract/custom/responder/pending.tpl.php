<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('ignore_pa_chat', $fields['ignore_pa_chat'], $object)?> <?php echo $fields['ignore_pa_chat']['trans'];?></label>
</div>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose what bot trigger element append after auto responder message')?></h4>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['pending_bot_id']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('pending_bot_id', $fields['pending_bot_id'], $object)?>
        </div>
        <div class="form-group">
            <label><?php echo $fields['pending_trigger_id']['trans'];?></label>
            <div id="pending-trigger-list-id"></div>
        </div>
    </div>
    <div class="col-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
        <div id="pending-trigger-preview-window">

        </div>
    </div>
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
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message', $fields['timeout_message'], $object)?>
        </div>
    </div>
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
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message_' . $i, $fields['timeout_message_' . $i], $object)?>
        </div>
    </div>
</div>
<?php endfor;?>