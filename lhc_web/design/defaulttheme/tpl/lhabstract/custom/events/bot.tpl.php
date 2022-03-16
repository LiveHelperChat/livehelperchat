<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('ignore_bot', $fields['ignore_bot'], $object)?> <?php echo $fields['ignore_bot']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('bot_offline', $fields['bot_offline'], $object)?> <?php echo $fields['bot_offline']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('append_bot', $fields['append_bot'], $object)?> <?php echo $fields['append_bot']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('keep_after_close', $fields['keep_after_close'], $object)?> <?php echo $fields['keep_after_close']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('append_intro_bot', $fields['append_intro_bot'], $object)?> <?php echo $fields['append_intro_bot']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo $fields['bot_id']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('bot_id', $fields['bot_id'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['trigger_id']['trans'];?></label>
    <div id="trigger-list-id"></div>
</div>

<p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','For trigger being executed on chat start also it has to have `Can be passed as argument` option enabled.');?></i></small></p>

<script>    
    $('select[name="AbstractInput_bot_id"]').change(function(){
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val(), { }, function(data) {
            $('#trigger-list-id').html(data);
        }).fail(function() {
            
        });
    });

    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="AbstractInput_bot_id"]').val() + '/<?php echo $object->trigger_id?>',  { }, function(data) {
        $('#trigger-list-id').html(data);
    }).fail(function() {

    });
</script>