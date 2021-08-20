<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
    <?php $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter(erLhcoreClassUser::instance()->getUserID());
    echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'new_dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
        'selected_id'    => $chat->dep_id,
        'css_class'      => 'form-control form-control-sm',
        'display_name'   => 'name',
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params'  => array_merge(array('sort' => 'sort_priority ASC, id ASC', 'limit' => '1000000'), ($userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array()))
    )); ?>
</div>