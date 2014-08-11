<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
<input type="text" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E-mail');?></label>
<input type="text" name="Email"  value="<?php echo htmlspecialchars($departament->email);?>" />

<div class="row">
	<div class="columns large-6">
		<label><input type="checkbox" name="Disabled" value="1" <?php if ($departament->disabled == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Disabled');?></label>
	</div>
	<div class="columns large-6">
		<label><input title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Will not be visible to visitor');?>" type="checkbox" name="Hidden" value="1" <?php if ($departament->hidden == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hidden');?></label>
	</div>
</div>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Delay in seconds before leave a message form is shown. 0 Means functionality is disabled, ');?></label>
<input type="text" name="delay_lm"  value="<?php echo htmlspecialchars($departament->delay_lm);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Priority');?></label>
<input type="text" name="Priority"  value="<?php echo htmlspecialchars($departament->priority);?>" />

<div class="section-container auto" data-section data-options="deep_linking: true">

  <section class="active">
		    <p class="title" data-section-title><a href="#onlinehours"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Automate online hours');?></a></p>	    
		    <div class="content" data-section-content data-slug="onlinehours">	
		    	<label><input ng-init="OnlineHoursActive=<?php if ($departament->online_hours_active == 1) : ?>true<?php else : ?>false<?php endif?>" type="checkbox" ng-model="OnlineHoursActive" name="OnlineHoursActive" value="1" <?php if ($departament->online_hours_active == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours/work days logic is active');?></label>

		    	<div ng-show="OnlineHoursActive">
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Workdays/work hours, during these days/hours chat will be active automatically');?></h4>
					<div class="row">
						<div class="columns large-3">
							<label><input type="checkbox" name="mod" value="1" <?php if ($departament->mod == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Monday');?></label>
							<label><input type="checkbox" name="tud" value="1" <?php if ($departament->tud == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Tuesday');?></label>
						</div>
						<div class="columns large-3">
							<label><input type="checkbox" name="wed" value="1" <?php if ($departament->wed == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Wednesday');?></label>
							<label><input type="checkbox" name="thd" value="1" <?php if ($departament->thd == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Thursday');?></label>
						</div>
						<div class="columns large-3">
							<label><input type="checkbox" name="frd" value="1" <?php if ($departament->frd == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Friday');?></label>
							<label><input type="checkbox" name="sad" value="1" <?php if ($departament->sad == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Saturday');?></label>
						</div>
						<div class="columns large-3">
							<label><input type="checkbox" name="sud" value="1" <?php if ($departament->sud == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Sunday');?></label>
						</div>
					</div>
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 1 - 24, minutes format 0 - 60');?></h4>
					<div class="row">
						<div class="columns large-4">
							<input type="text" name="StartHour" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hours from, E.g. 8');?>" value="<?php echo htmlspecialchars($departament->start_hour_front);?>" />
						</div>
						<div class="columns large-2">
							<input type="text" name="StartHourMinit" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Minutes from, E.g. 30');?>" value="<?php echo htmlspecialchars($departament->start_minutes_front);?>" />
						</div>
						<div class="columns large-4">
							<input type="text" name="EndHour" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Hours to, E.g. 17');?>" value="<?php echo htmlspecialchars($departament->end_hour_front);?>" />
						</div>
						<div class="columns large-2">
							<input type="text" name="EndHourMinit" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Minutes to, E.g. 30');?>" value="<?php echo htmlspecialchars($departament->end_minutes_front);?>" />
						</div>
					</div>
				</div>

  			</div>
  </section>

	<section>
		    <p class="title" data-section-title><a href="#notifications"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Notifications');?></a></p>	    
		    <div class="content" data-section-content data-slug="notifications">	
		    		    
				<div class="row">
					<div class="columns small-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP recipients');?></label>
						<input type="text" name="XMPPRecipients"  value="<?php echo htmlspecialchars($departament->xmpp_recipients);?>" /></div>
					<div class="columns small-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP group recipients');?></label>
						<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E.g somechat@conference.server.org/LiveChat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These messages will be send as group messages');?>" name="XMPPRecipientsGroup"  value="<?php echo htmlspecialchars($departament->xmpp_group_recipients);?>" />
					</div>
				</div>	
				
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform about new chats using');?></h4>
							
				<label><input type="checkbox" name="inform_options[]" value="xmp" <?php if (in_array('xmp', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP messages');?></label>
				<label><input type="checkbox" name="inform_options[]" value="mail" <?php if (in_array('mail', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Mail messages');?></label>
								
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','How many seconds chat can be pending before about chat is informed a staff');?></label>
				<input type="text" name="inform_delay"  value="<?php echo htmlspecialchars($departament->inform_delay);?>" />
				
				
				<label><input type="checkbox" name="inform_unread"  value="on" <?php echo $departament->inform_unread == 1 ? 'checked="checked"' : '';?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform about unread messages if from last unread user message have passed (seconds)');?></label> 
				<input type="text" name="inform_unread_delay" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please enter value in seconds');?>" value="<?php echo htmlspecialchars($departament->inform_unread_delay);?>" />
				
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform then chat is accepted by one of the staff members using');?></h4>
				<label><input type="checkbox" name="inform_options[]" value="xmp_accepted" <?php if (in_array('xmp_accepted', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','XMPP messages');?></label>
				<label><input type="checkbox" name="inform_options[]" value="mail_accepted" <?php if (in_array('mail_accepted', $departament->inform_options_array)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Mail messages');?></label>
								
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Other');?></h4>
				<label><input type="checkbox" name="inform_close" value="1" <?php if ($departament->inform_close == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform then chat is closed by operator, only mail notification is send.');?></label>
								
			</div>
	</section>
  
   
  <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actworkflow')) : ?>
  <section>
		    <p class="title" data-section-title><a href="#chattransfer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Chat transfer worklow');?></a></p>	    
		    <div class="content" data-section-content data-slug="chattransfer">	
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','To what department chat should be transfered if it is not accepted');?></label>
				<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
										'input_name'     => 'TansferDepartmentID',
										'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','None'),
										'display_name'   => 'name',
										'selected_id'    => $departament->department_transfer_id,
										'list_function'  => 'erLhcoreClassModelDepartament::getList',
										'list_function_params'  => array_merge(array('limit' => '1000000'),$limitDepartments),
				)); ?>
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Timeout in seconds before chat is transfered to another department. Minimum 5 seconds.');?></label>
				<input type="text" name="TransferTimeout" value="<?php echo htmlspecialchars($departament->transfer_timeout);?>" />
								
				<label><input type="checkbox" name="nc_cb_execute" value="on" <?php if ($departament->nc_cb_execute == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Execute new chat logic again for recipient department?');?></label>
				<label><input type="checkbox" name="na_cb_execute" value="on" <?php if ($departament->na_cb_execute == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Execute unanswered chat logic again for recipient department?');?></label>
				
						    	
  			</div>
  </section>
  <?php endif; ?>
  
   
  <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actautoassignment')) : ?>
  <section>
		<p class="title" data-section-title><a href="#autoassignment"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Auto assignment');?></a></p>	    
		<div class="content" data-section-content data-slug="autoassignment">	
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Active');?> <input type="checkbox" ng-init="AutoAssignActive=<?php if ($departament->active_balancing == 1) : ?>true<?php else : ?>false<?php endif;?>" ng-model="AutoAssignActive" name="AutoAssignActive" value="on" <?php if ($departament->active_balancing == 1) : ?>checked="checked"<?php endif;?> /></label>

		    	<div ng-show="AutoAssignActive">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Maximum number of active chats user can have at a time, 0 - unlimited');?></label>
					<input type="text" name="MaxNumberActiveChats" value="<?php echo htmlspecialchars($departament->max_active_chats);?>" />
					
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/operatorsbalancing','Automatically assign chat to another operator if operator did not accepted chat in seconds, 0 - disabled');?></label>
					<input type="text" name="MaxWaitTimeoutSeconds" value="<?php echo htmlspecialchars($departament->max_timeout_seconds);?>" />
				</div> 						    	
  		</div>
  </section>
  <?php endif; ?>
  
  <section>
		    <p class="title" data-section-title><a href="#miscellaneous"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Miscellaneous');?></a></p>	    
		    <div class="content" data-section-content data-slug="miscellaneous">	
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','This field is max 50 characters length and can be used for any purpose by extensions. This field is also indexed.');?></label>
				<input type="text" name="Identifier"  value="<?php echo htmlspecialchars($departament->identifier);?>" />						    	    	
  			</div>
  </section>    
  
</div>
    
