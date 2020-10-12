<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Event');?></label>
    <input type="text" class="form-control" name="event"  value="<?php echo htmlspecialchars($item->event);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a bot');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'bot_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose a bot'),
        'selected_id'    => $item->bot_id,
        'list_function'  => 'erLhcoreClassModelGenericBotBot::getList',
        'list_function_params'  => array()
    ) ); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a trigger');?></label>
    <div id="trigger-list-id"></div>
</div>

<script>
    $('select[name="bot_id"]').change(function(){
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val(), { }, function(data) {
            $('#trigger-list-id').html(data);
        }).fail(function() {

        });
    });
    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="bot_id"]').val() + '/<?php echo $item->trigger_id?>',  { }, function(data) {
        $('#trigger-list-id').html(data);
    }).fail(function() {

    });
</script>



<div class="form-group">
    <label><input type="checkbox" value="on" name="disabled" <?php echo $item->disabled == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?></label>
</div>