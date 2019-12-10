<?php if (!isset($autoResponderOptions['hide_visitor_not_replying_bot']) || $autoResponderOptions['hide_visitor_not_replying_bot'] === false) : ?>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose what bot trigger element append after auto responder message')?></h4>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['nreply_bot_id']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('nreply_bot_id', $fields['nreply_bot_id'], $object)?>
        </div>
        <div class="form-group">
            <label><?php echo $fields['nreply_trigger_id']['trans'];?></label>
            <div id="nreply-trigger-list-id"></div>
        </div>
    </div>
    <div class="col-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
        <div id="nreply-trigger-preview-window">
        </div>
    </div>
</div>
<?php endif; ?>

<?php for ($i = 1; $i <= 5; $i++) : ?>
<div class="row">

    <?php if (!isset($autoResponderOptions['hide_visitor_not_replying_timeout']) || $autoResponderOptions['hide_visitor_not_replying_timeout'] === false) : ?>
    <div class="col-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout_reply_' . $i]['trans'];?></label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout_reply_' . $i, $fields['wait_timeout_reply_' . $i], $object)?>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_reply_message_' . $i]['trans'];?></label>

        <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_timeout_reply_message_'.$i.']'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>

        <?php echo erLhcoreClassAbstract::renderInput('timeout_reply_message_' . $i, $fields['timeout_reply_message_' . $i], $object)?>
        </div>
    </div>
</div>
<?php endfor;?>