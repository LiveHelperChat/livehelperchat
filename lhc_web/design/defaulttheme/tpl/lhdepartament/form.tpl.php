<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
<input type="text" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E-mail');?></label>
<input type="text" name="Email"  value="<?php echo htmlspecialchars($departament->email);?>" />

<label><input type="checkbox" name="Disabled" value="1" <?php if ($departament->disabled == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Disabled');?></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Delay in seconds before leave a message form is shown. 0 Means functionality is disabled, ');?></label>
<input type="text" name="delay_lm"  value="<?php echo htmlspecialchars($departament->delay_lm);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Priority');?></label>
<input type="text" name="Priority"  value="<?php echo htmlspecialchars($departament->priority);?>" />

<div class="section-container auto" data-section data-options="deep_linking: true">

  <section class="active">
		    <p class="title" data-section-title><a href="#onlinehours"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Automate online hours');?></a></p>	    
		    <div class="content" data-section-content data-slug="onlinehours">	
		    	<label><input type="checkbox" name="OnlineHoursActive" value="1" <?php if ($departament->online_hours_active == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours/work days logic is active');?></label>

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
				
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 1 - 24');?></h4>
				<div class="row">
					<div class="columns large-6">
						<input type="text" name="StartHour" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','From, E.g. 8');?>" value="<?php echo htmlspecialchars($departament->start_hour);?>" />
					</div>
					<div class="columns large-6">
						<input type="text" name="EndHour" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','To, E.g. 17');?>" value="<?php echo htmlspecialchars($departament->end_hour);?>" />
					</div>
				</div>


  			</div>
  </section>

	<section>
		    <p class="title" data-section-title><a href="#notifications"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Notifications about new chats');?></a></p>	    
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

				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Other');?></h4>
				<label><input type="checkbox" name="inform_close" value="1" <?php if ($departament->inform_close == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Inform then chat is closed by operator, only mail notification is send.');?></label>
								
			</div>
	</section>
  
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
										'list_function_params'  => array('limit' => '1000000'),
				)); ?>
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Timeout in seconds before chat is transfered to another department. Minimum 5 seconds.');?></label>
				<input type="text" name="TransferTimeout" value="<?php echo htmlspecialchars($departament->transfer_timeout);?>" />
						    	
  			</div>
  </section>
  
  <section>
		    <p class="title" data-section-title><a href="#miscellaneous"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Miscellaneous');?></a></p>	    
		    <div class="content" data-section-content data-slug="miscellaneous">	
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','This field is max 50 characters length and can be used for any purpose by extensions. This field is also indexed.');?></label>
				<input type="text" name="Identifier"  value="<?php echo htmlspecialchars($departament->identifier);?>" />						    	    	
  			</div>
  </section>    
  
</div>
    
