<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/newcannedmsg')?>" method="post">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?></label>
    <textarea name="Message"><?php echo htmlspecialchars($msg->msg);?></textarea>

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
    <input type="text" name="Delay" value="<?php echo $msg->delay?>" />

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></label>
    <input type="text" name="Position" value="<?php echo $msg->position?>" />

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
							'input_name'     => 'DepartmentID',
							'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any'),
							'display_name'   => 'name',
							'selected_id'    => $msg->department_id,
							'list_function'  => 'erLhcoreClassModelDepartament::getList',
							'list_function_params'  => array('limit' => '1000000'),
	)); ?>

    <ul class="button-group radius">
    <li><input type="submit" class="small button" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
	<li><input type="submit" class="small button" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
	</ul>

</form>
