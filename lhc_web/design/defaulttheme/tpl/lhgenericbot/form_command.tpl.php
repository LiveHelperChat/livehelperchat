<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Command, do not add ! prefix');?></label>
    <input type="text" class="form-control" name="command" placeholder="go2bot" value="<?php echo htmlspecialchars($item->command);?>" />
</div>

<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department, if you do not choose command will be available to all departments.');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'dep_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control',
        'selected_id'    => $item->dep_id,
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params'  => array(),
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any')
    ) ); ?>
</div>

<div class="form-group" ng-non-bindable>
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



