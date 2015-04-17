<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title');?></label>
    <input type="text" class="form-control" name="Title" value="<?php echo htmlspecialchars($canned_message->title);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Explain');?></label>
    <input type="text" class="form-control" name="ExplainHover" value="<?php echo htmlspecialchars($canned_message->explain);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
    <textarea class="form-control" name="Message"><?php echo htmlspecialchars($canned_message->msg);?></textarea>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
    <textarea class="form-control" name="FallbackMessage"><?php echo htmlspecialchars($canned_message->fallback_msg);?></textarea>
</div>

<label><input type="checkbox" name="AutoSend" value="on" <?php $canned_message->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>

<div class="form-group">
   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
   <input type="text" class="form-control" name="Delay" value="<?php echo $canned_message->delay?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></label>
    <input type="text" class="form-control" name="Position" value="<?php echo $canned_message->position?>" />
</div>

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

if (empty($limitDepartments)) {
	$params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any');
}

echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>