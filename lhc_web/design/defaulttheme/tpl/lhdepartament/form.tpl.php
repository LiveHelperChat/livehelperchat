<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E-mail');?></label>
    <input type="text" class="form-control" name="Email"  value="<?php echo htmlspecialchars($departament->email);?>" />
</div>

<div class="row form-group">
	<div class="col-md-6">
		<label><input type="checkbox" name="Disabled" value="1" <?php if ($departament->disabled == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Disabled');?></label>
	</div>
	<div class="col-md-6">
		<label><input title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Will not be visible to visitor');?>" type="checkbox" name="Hidden" value="1" <?php if ($departament->hidden == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hidden');?></label>
	</div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Delay in seconds before leave a message form is shown. 0 Means functionality is disabled, ');?></label>
    <input type="text" class="form-control" name="delay_lm"  value="<?php echo htmlspecialchars($departament->delay_lm);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Priority');?></label>
    <input type="text" class="form-control" name="Priority"  value="<?php echo htmlspecialchars($departament->priority);?>" />
</div>

<div role="tabpanel" class="form-group">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#onlinehours" aria-controls="onlinehours" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Automate online hours');?></a></li>
			<li role="presentation"><a href="#notifications" aria-controls="notifications" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Notifications');?></a></li>
			
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actworkflow')) : ?>
			<li role="presentation"><a href="#chattransfer" aria-controls="chattransfer" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Chat transfer worklow');?></a></li>
			<?php endif;?>
			
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actautoassignment')) : ?>
			<li role="presentation"><a href="#autoassignment" aria-controls="autoassignment" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Auto assignment');?></a></li>
			<?php endif;?>
			
			<li role="presentation"><a href="#miscellaneous" aria-controls="miscellaneous" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Miscellaneous');?></a></li>
		</ul>
		
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="onlinehours">
			    <label><input ng-init="OnlineHoursActive=<?php if ($departament->online_hours_active == 1) : ?>true<?php else : ?>false<?php endif?>" type="checkbox" ng-model="OnlineHoursActive" name="OnlineHoursActive" value="1" <?php if ($departament->online_hours_active == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours/work days logic is active');?></label>

		    	<div ng-show="OnlineHoursActive">
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Workdays/work hours, during these days/hours chat will be active automatically');?></h4>
					
					<label><input type="checkbox" name="mod" value="1" <?php if ($departament->mod == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Monday');?></label>
					<label><input type="checkbox" name="tud" value="1" <?php if ($departament->tud == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Tuesday');?></label>
					<label><input type="checkbox" name="wed" value="1" <?php if ($departament->wed == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Wednesday');?></label>
					<label><input type="checkbox" name="thd" value="1" <?php if ($departament->thd == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Thursday');?></label>
					<label><input type="checkbox" name="frd" value="1" <?php if ($departament->frd == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Friday');?></label>
					<label><input type="checkbox" name="sad" value="1" <?php if ($departament->sad == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Saturday');?></label>
					<label><input type="checkbox" name="sud" value="1" <?php if ($departament->sud == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Sunday');?></label>
					
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 1 - 24, minutes format 0 - 60');?></h4>
					
					<div class="form-inline">
					   <div class="form-group">
					       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hours from');?></label>
					       <input type="text" class="form-control" name="StartHour" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hours from, E.g. 8');?>" value="<?php echo htmlspecialchars($departament->start_hour_front);?>" />
					       
					       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Minutes from');?></label>
					       <input type="text" class="form-control" name="StartHourMinit" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Minutes from, E.g. 30');?>" value="<?php echo htmlspecialchars($departament->start_minutes_front);?>" />
					       
					       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hours to');?></label>
					       <input type="text" class="form-control" name="EndHour" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hours to, E.g. 17');?>" value="<?php echo htmlspecialchars($departament->end_hour_front);?>" />
					       
					       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Minutes to');?></label>
					       <input type="text" class="form-control" name="EndHourMinit" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Minutes to, E.g. 30');?>" value="<?php echo htmlspecialchars($departament->end_minutes_front);?>" />
					   </div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane" id="notifications">
			
			     <?php include(erLhcoreClassDesign::designtpl('lhdepartment/xmpp_enabled.tpl.php'));?>
			     
			     <?php if ($department_xmpp_enabled == true) : ?>	
			     <div class="row form-group">
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP recipients');?></label>
						<input type="text" class="form-control" name="XMPPRecipients"  value="<?php echo htmlspecialchars($departament->xmpp_recipients);?>" /></div>
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP group recipients');?></label>
						<input type="text" class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E.g somechat@conference.server.org/LiveChat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These messages will be send as group messages');?>" name="XMPPRecipientsGroup"  value="<?php echo htmlspecialchars($departament->xmpp_group_recipients);?>" />
					</div>
				</div>	
				<?php endif;?>
				
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform about new chats using');?></h4>
				
				<?php if ($department_xmpp_enabled == true) : ?>
				<label><input type="checkbox" name="inform_options[]" value="xmp" <?php if (in_array('xmp', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP messages');?></label><br>
				<?php endif;?>
				
				<label><input type="checkbox" name="inform_options[]" value="mail" <?php if (in_array('mail', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Mail messages');?></label>
								
				<div class="form-group">
				    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','How many seconds chat can be pending before about chat is informed a staff');?></label>
				    <input type="text" class="form-control" name="inform_delay"  value="<?php echo htmlspecialchars($departament->inform_delay);?>" />
				</div>
				
				<div class="form-group">
				    <label><input type="checkbox" name="inform_unread"  value="on" <?php echo $departament->inform_unread == 1 ? 'checked="checked"' : '';?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform about unread messages if from last unread user message have passed (seconds)');?></label> 
				    <input type="text" class="form-control" name="inform_unread_delay" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please enter value in seconds');?>" value="<?php echo htmlspecialchars($departament->inform_unread_delay);?>" />
				</div>
				
				<div class="form-group">
				    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform then chat is accepted by one of the staff members using');?></h4>
				    <?php if ($department_xmpp_enabled == true) : ?><label><input type="checkbox" name="inform_options[]" value="xmp_accepted" <?php if (in_array('xmp_accepted', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP messages');?></label><?php endif;?>
				    <label><input type="checkbox" name="inform_options[]" value="mail_accepted" <?php if (in_array('mail_accepted', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Mail messages');?></label>
				</div>
								
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Other');?></h4>
				<label><input type="checkbox" name="inform_close" value="1" <?php if ($departament->inform_close == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform then chat is closed by operator, only mail notification is send.');?></label>
			</div>
			
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actworkflow')) : ?>
			 <div role="tabpanel" class="tab-pane" id="chattransfer">
			     <div class="form-group">
    			     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','To what department chat should be transfered if it is not accepted');?></label>
    				<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
    										'input_name'     => 'TansferDepartmentID',
    										'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','None'),
    										'display_name'   => 'name',
    				                        'css_class'      => 'form-control',
    										'selected_id'    => $departament->department_transfer_id,
    										'list_function'  => 'erLhcoreClassModelDepartament::getList',
    										'list_function_params'  => array_merge(array('limit' => '1000000'),$limitDepartments),
    				)); ?>
				</div>
				
				<div class="form-group">
				    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Timeout in seconds before chat is transfered to another department. Minimum 5 seconds.');?></label>
				    <input type="text" class="form-control" name="TransferTimeout" value="<?php echo htmlspecialchars($departament->transfer_timeout);?>" />
				</div>
				
				<div class="form-group">			
				    <label><input type="checkbox" name="nc_cb_execute" value="on" <?php if ($departament->nc_cb_execute == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Execute new chat logic again for recipient department?');?></label><br>
				    <label><input type="checkbox" name="na_cb_execute" value="on" <?php if ($departament->na_cb_execute == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Execute unanswered chat logic again for recipient department?');?></label>
				</div>	  
			 </div>
			<?php endif;?>
			
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actautoassignment')) : ?>
			<div role="tabpanel" class="tab-pane" id="autoassignment">
			    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Active');?> <input type="checkbox" ng-init="AutoAssignActive=<?php if ($departament->active_balancing == 1) : ?>true<?php else : ?>false<?php endif;?>" ng-model="AutoAssignActive" name="AutoAssignActive" value="on" <?php if ($departament->active_balancing == 1) : ?>checked="checked"<?php endif;?> /></label>

		    	<div ng-show="AutoAssignActive">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Maximum number of active chats user can have at a time, 0 - unlimited');?></label>
					<input type="text" class="form-control" name="MaxNumberActiveChats" value="<?php echo htmlspecialchars($departament->max_active_chats);?>" />
					
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Automatically assign chat to another operator if operator did not accepted chat in seconds, 0 - disabled');?></label>
					<input type="text" class="form-control" name="MaxWaitTimeoutSeconds" value="<?php echo htmlspecialchars($departament->max_timeout_seconds);?>" />
				</div> 
		    </div>
			<?php endif;?>
			
			<div role="tabpanel" class="tab-pane" id="miscellaneous">
			   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','This field is max 50 characters length and can be used for any purpose by extensions. This field is also indexed.');?></label>
			   <input type="text" class="form-control" name="Identifier"  value="<?php echo htmlspecialchars($departament->identifier);?>" />
		    </div>
			
		</div>
</div>