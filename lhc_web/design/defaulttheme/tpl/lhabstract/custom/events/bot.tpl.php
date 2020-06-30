<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('bot_offline', $fields['bot_offline'], $object)?> <?php echo $fields['bot_offline']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('append_bot', $fields['append_bot'], $object)?> <?php echo $fields['append_bot']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo $fields['bot_id']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('bot_id', $fields['bot_id'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['trigger_id']['trans'];?></label>
    <div id="trigger-list-id"></div>
</div>

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