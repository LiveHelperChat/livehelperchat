<div class="row">
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Command, do not add ! prefix');?>*</label>
            <input type="text" class="form-control form-control-sm" name="command" placeholder="go2bot" value="<?php echo htmlspecialchars($item->command);?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Sub command');?></label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="sub_command" placeholder="--silent" value="<?php echo htmlspecialchars($item->sub_command);?>" />
        </div>
    </div>
</div>

<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Info message');?></label>
    <input type="text" maxlength="100" class="form-control form-control-sm" name="info_msg" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Subject added!');?>" value="<?php echo htmlspecialchars($item->info_msg);?>" />
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Info message is usefull if you want to show operator that command was executed without storing any real message within chat.');?></i></small></p>
</div>

<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department, if you do not choose command will be available to all departments.');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'dep_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control form-control-sm',
        'selected_id'    => $item->dep_id,
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => false],erLhcoreClassUserDep::conditionalDepartmentFilter()),
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any')
    ) ); ?>
</div>

<div class="row pb-2">
    <div class="col-12">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Shortcut, optional');?></label>
    </div>
    <div class="col-6">
        <select name="shortcut_1" class="form-control form-control-sm">
            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose')?></option>
            <option value="Alt" <?php if ($item->shortcut_1 == 'Alt') : ?>selected="selected"<?php endif; ?> >Alt</option>
            <option value="Ctrl" <?php if ($item->shortcut_1 == 'Ctrl') : ?>selected="selected"<?php endif; ?> >Ctrl</option>
            <option value="Alt+Ctrl" <?php if ($item->shortcut_1 == 'Alt+Ctrl') : ?>selected="selected"<?php endif; ?>>Ctrl+Alt</option>
        </select>
    </div>
    <div class="col-6">
        <select name="shortcut_2" class="form-control form-control-sm">
            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose')?></option>
            <?php foreach ([
                    'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r',
                               's','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9',
                'f1','f2','f3','f4','f5','f6','f7','f8','f9','f10','f11','f12'
                           ] as $letter) : ?>
                <option value="<?php echo $letter?>" <?php if ($letter == $item->shortcut_2) : ?>selected="selected"<?php endif?> ><?php echo $letter?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a bot');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'bot_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control form-control-sm',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose a bot'),
        'selected_id'    => $item->bot_id,
        'list_function'  => 'erLhcoreClassModelGenericBotBot::getList',
        'list_function_params'  => ['sort' => '`name` ASC', 'limit' => false],
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



