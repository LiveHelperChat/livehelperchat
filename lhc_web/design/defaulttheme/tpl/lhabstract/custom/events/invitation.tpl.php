<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['name']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
        </div>
    </div>
    <div class="col-6">
        <label><?php echo $fields['parent_id']['trans'];?><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/parentinvitation'});" class="material-icons text-muted">help</a>
        </label>
        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
            'input_name'     => 'AbstractInput_parent_id',
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb', 'Choose a parent invitation'),
            'selected_id'    => [$object->parent_id],
            'data_prop'      => 'data-limit="1"',
            'css_class'      => 'form-control',
            'type'           => 'radio',
            'display_name'   => 'name',
            'no_selector'    => true,
            'list_function_params' => ($object->id > 0 ? array('limit' => false,'filternot' => ['id' => $object->id]) : ['limit' => false]),
            'list_function'  => 'erLhAbstractModelProactiveChatInvitation::getList',
        )); ?>
    </div>
    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>
</div>


<div class="form-group">
<label><?php echo erLhcoreClassAbstract::renderInput('disabled', $fields['disabled'], $object)?> <?php echo $fields['disabled']['trans'];?></label>
</div>

<?php $translatableItem = array('identifier' => 'operator_name'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">		
<label><?php echo $fields['position']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('position', $fields['position'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['siteaccess']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('siteaccess', $fields['siteaccess'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['time_on_site']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('time_on_site', $fields['time_on_site'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['delay']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('delay', $fields['delay'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['delay_init']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('delay_init', $fields['delay_init'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['pageviews']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('pageviews', $fields['pageviews'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['referrer']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('referrer', $fields['referrer'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['hide_after_ntimes']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('hide_after_ntimes', $fields['hide_after_ntimes'], $object)?>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['expires_after']['trans'];?></label>
            <select class="form-control form-control-sm" name="AbstractInput_expires_after">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Permanent (visitor has to close invitation)');?></option>
                <option value="60" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
                <option value="300" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                <option value="600" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                <option value="1800" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                <option value="3600" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
                <option value="7200" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="14400" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="28800" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="57600" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="86400" <?php if (isset($object->{$fields['expires_after']['main_attr']}['expires_after']) && $object->{$fields['expires_after']['main_attr']}['expires_after'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['next_inv_time']['trans'];?></label>
            <select class="form-control form-control-sm" name="AbstractInput_next_inv_time">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Follows system default settings');?></option>
                <option value="60" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
                <option value="300" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                <option value="600" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                <option value="1800" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                <option value="3600" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
                <option value="7200" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="14400" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="28800" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="57600" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                <option value="86400" <?php if (isset($object->{$fields['next_inv_time']['main_attr']}['next_inv_time']) && $object->{$fields['next_inv_time']['main_attr']}['next_inv_time'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
            </select>
        </div>
    </div>
</div>

<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('requires_email', $fields['requires_email'], $object)?> <?php echo $fields['requires_email']['trans'];?></label>
</div>

<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('requires_username', $fields['requires_username'], $object)?> <?php echo $fields['requires_username']['trans'];?></label>
</div>

<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('requires_phone', $fields['requires_phone'], $object)?> <?php echo $fields['requires_phone']['trans'];?></label>
</div>

<div class="form-group">
<label><?php echo $fields['show_on_mobile']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('show_on_mobile', $fields['show_on_mobile'], $object)?>
</div>

<div class="row">

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('show_everytime', $fields['show_everytime'], $object)?> <?php echo $fields['show_everytime']['trans'];?></label>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('show_after_chat', $fields['show_after_chat'], $object)?> <?php echo $fields['show_after_chat']['trans'];?></label>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('lock_department', $fields['lock_department'], $object)?> <?php echo $fields['lock_department']['trans'];?></label>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('show_random_operator', $fields['show_random_operator'], $object)?> <?php echo $fields['show_random_operator']['trans'];?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('assign_to_randomop', $fields['assign_to_randomop'], $object)?> <?php echo $fields['assign_to_randomop']['trans'];?></label>
        </div>
    </div>
</div>

<div class="form-group">		
<label><?php echo $fields['operator_ids']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('operator_ids', $fields['operator_ids'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['identifier']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('identifier', $fields['identifier'], $object)?>
</div>

<div class="form-group">		
<label>
    <?php echo $fields['tag']['trans'];?>
    <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/invitationtag'});" class="material-icons text-muted">help</a>
</label>
<?php echo erLhcoreClassAbstract::renderInput('tag', $fields['tag'], $object)?>
</div>

<?php $showAnyDepartment = !empty($limitDepartments = erLhcoreClassUserDep::conditionalDepartmentFilter()); ?>
<div class="form-group">

    <div class="d-flex">
        <div class="d-inline pe-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
        </div>
        <div class="d-inline pe-2">
            <?php
            $params = array (
                'input_name'     => 'cannedDepartmentGroup',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'selected_id'    => 0,
                'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList',
                'list_function_params'  => array('limit' => false,'sort' => '`name` ASC'),
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose department group')
            );
            echo erLhcoreClassRenderHelper::renderCombobox($params);
            ?>
        </div>
        <div class="d-inline">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" id="check-by-department-group" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Check all departments from selected department group');?>" class="btn btn-sm btn-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Check');?></button>
                <button type="button" id="uncheck-check-by-department-group" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Uncheck all departments from selected department group');?>" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Uncheck');?></button>
            </div>
        </div>
    </div>

    <div class="row" style="max-height: 500px; overflow: auto">
        <?php
        $params = array (
            'input_name'     => 'DepartmentID[]',
            'display_name'   => 'name',
            'css_class'      => 'form-control',
            'multiple'       => true,
            'wrap_prepend'   => '<div class="col-4">',
            'wrap_append'    => '</div>',
            'selected_id'    => $object->dep_ids_front,
            'list_function'  => 'erLhcoreClassModelDepartament::getList',
            'list_function_params'  => array_merge(array('sort' => '`name` ASC', 'limit' => false), $limitDepartments)
        );

        if (empty($limitDepartments) || (isset($showAnyDepartment) && $showAnyDepartment == true)) {
            $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any');
        }

        echo erLhcoreClassRenderHelper::renderCheckbox( $params ); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('#check-by-department-group,#uncheck-check-by-department-group').click(function(){
            var id = $(this).attr('id');
            $.getJSON(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/depbydepgroup?d='+$('#id_cannedDepartmentGroup').val(), function(data) {
                data.items.forEach( function(item) {
                    $('#chk-DepartmentID-'+item).prop('checked', id == 'check-by-department-group');
                })
            });
        });
    });
</script>

<div class="form-group">
<label><?php echo $fields['campaign_id']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('campaign_id', $fields['campaign_id'], $object)?>
</div>

<?php $translatableItem = array('identifier' => 'message'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<?php $translatableItem = array('identifier' => 'message_returning'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<?php $translatableItem = array('identifier' => 'message_returning_nick'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">
<label><?php echo $fields['autoresponder_id']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('autoresponder_id', $fields['autoresponder_id'], $object)?>
</div>