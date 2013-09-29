<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
<input type="text" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E-mail');?></label>
<input type="text" name="Email"  value="<?php echo htmlspecialchars($departament->email);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Priority');?></label>
<input type="text" name="Priority"  value="<?php echo htmlspecialchars($departament->priority);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','To what department chat should be transfered if it is not accepted');?></label>
<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
						'input_name'     => 'TansferDepartmentID',
						'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','None'),
						'display_name'   => 'name',
						'selected_id'    => $departament->department_transfer_id,
						'list_function'  => 'erLhcoreClassModelDepartament::getList',
						'list_function_params'  => array('limit' => '1000000'),
)); ?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Timeout in seconds before chat is transfered to another department. Minimum 5 seconds.');?></label>
<input type="text" name="TransferTimeout" value="<?php echo htmlspecialchars($departament->transfer_timeout);?>" />