<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>		

    <div class="row" style="max-height: 500px; overflow: auto">

<?php
$params = array (
		'input_name'     => 'DepartmentID[]',
		'display_name'   => 'name',
        'css_class'      => 'form-control',
		'multiple'       => true,
		'wrap_prepend'   => '<div class="col-4">',
		'wrap_append'    => '</div>',
		'selected_id'    => $canned_message->department_ids_front,
		'list_function'  => 'erLhcoreClassModelDepartament::getList',
		'list_function_params'  => array_merge(array('sort' => 'sort_priority ASC, id ASC', 'limit' => '1000000'), $limitDepartments)
);

if (empty($limitDepartments) || (isset($showAnyDepartment) && $showAnyDepartment == true)) {
    $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any');
}

echo erLhcoreClassRenderHelper::renderCheckbox( $params ); ?>
    </div></div>