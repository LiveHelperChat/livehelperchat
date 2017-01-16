<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>		
<?php 
$params = array (
		'input_name'     => 'DepartmentID',			
		'display_name'   => 'name',
        'css_class'      => 'form-control',
		'selected_id'    => $canned_message->department_id,
		'list_function'  => 'erLhcoreClassModelDepartament::getList',
		'list_function_params'  => array_merge(array('limit' => '1000000'),$limitDepartments)
);

if (empty($limitDepartments) || (isset($showAnyDepartment) && $showAnyDepartment == true)) {
	$params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any');
}

echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>