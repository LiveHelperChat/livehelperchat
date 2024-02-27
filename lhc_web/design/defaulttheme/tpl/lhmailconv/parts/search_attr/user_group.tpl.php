<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_group_title.tpl.php')); ?>
<div class="col-md-2">
    <div class="form-group">
        <label><?php echo $userGroupTitle['user_group'];?></label>
        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
            'input_name'     => 'group_ids[]',
            'optional_field' => $userGroupTitle['user_group_select'],
            'selected_id'    => $input->group_ids,
            'css_class'      => 'form-control',
            'display_name'   => 'name',
            'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(false, true),
            'list_function'  => 'erLhcoreClassModelGroup::getList'
        )); ?>
    </div>
</div>