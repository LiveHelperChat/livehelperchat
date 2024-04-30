<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" maxlength="50" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name);?>" />
</div>

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

<div class="row">
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Info message');?></label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="info_msg" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Subject added!');?>" value="<?php echo htmlspecialchars($item->info_msg);?>" />
            <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Info message is usefull if you want to show operator that command was executed without storing any real message within chat.');?></i></small></p>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?>
                <a class="live-help-tooltip" data-placement="top" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','The smaller the position value the higher the command will appear in the list')?>" data-bs-toggle="tooltip" ><i class="material-icons">&#xE887;</i></a>
            </label>
            <input type="number" class="form-control form-control-sm" name="position" value="<?php echo htmlspecialchars($item->position)?>" />
        </div>
    </div>
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

<div class="form-group">
    <label><input type="checkbox" name="enabled_display" <?php if ($item->enabled_display == 1) : ?>checked="checked"<?php endif;?> value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Enabled for display')?></label>
</div>

<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Custom arguments fields, visible in modal window if enabled for display');?></h6>

<div class="fields-custom mb-2">
    <div class="row" id="add-field-row">
        <div class="col-3">
            <input class="form-control form-control-sm" id="custom_field_name" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name of the field')?>" />
        </div>
        <div class="col-2">
            <input class="form-control form-control-sm" id="custom_field_placeholder" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Placeholder')?>" />
        </div>
        <div class="col-2">
            <select id="custom_field_type" class="form-control form-control-sm">
                <option value="text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Text field')?></option>
                <option value="textarea"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Text area')?></option>
            </select>
        </div>
        <div class="col-2">
            <input id="custom_field_rows" class="form-control form-control-sm" type="number" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Number of rows in case Text area field tye is chosen')?>" />
        </div>
        <div class="col-2">
            <select id="custom_field_required" class="form-control form-control-sm">
                <option value="required"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Field is required')?></option>
                <option value="optional"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Field is optional')?></option>
            </select>
        </div>
        <div class="col-1">
            <button name="custom_field_add" id="field-action-button" data-add="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Add')?>" data-update="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Update')?>" type="button" class="btn btn-sm btn-success w-100"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Add')?></button>
        </div>
    </div>
    <div id="field-rows-container" edit-action="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit');?>" required-field="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Required');?>" type-field="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Type');?>" rows-field="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Rows');?>" placeholder-field="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Placeholder');?>" name-field="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?>" remove-action="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Remove');?>">

    </div>
</div>

<script>
    window.botCommandFields = <?php echo json_encode($item->fields_array)?>;
    $(document).ready(function() {
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
    });
</script>



