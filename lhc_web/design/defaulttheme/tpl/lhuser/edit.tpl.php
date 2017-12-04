<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Edit user');?> - <?php echo htmlspecialchars($user->name_support)?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<ul class="nav nav-pills" role="tablist">
	<li role="presentation" <?php if ($tab == '') : ?> class="active" <?php endif;?>><a href="#account" aria-controls="account" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></li>
	<li role="presentation" <?php if ($tab == 'tab_departments') : ?>class="active"<?php endif;?>><a href="#departments" aria-controls="departments" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></li>
	<li role="presentation" <?php if ($tab == 'tab_pending') : ?>class="active"<?php endif;?>><a href="#pending" aria-controls="pending" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Pending chats');?></a></li>
	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhpermission','see_permissions_users')) : ?>
	<li role="presentation" <?php if ($tab == 'tab_permission') : ?>class="active"<?php endif;?>><a href="#permission" aria-controls="permission" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Permissions');?></a></li>
	<?php endif;?>

	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs/custom_multiinclude_tab.tpl.php'));?>

</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="account">

	   <?php include(erLhcoreClassDesign::designtpl('lhuser/account/above_account_edit_multiinclude.tpl.php'));?>

	   <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Do not enter a password unless you want to change it');?></div>
	   <br />

	   <form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#account" method="post" autocomplete="off" enctype="multipart/form-data">
	        
	        <?php include(erLhcoreClassDesign::designtpl('lhuser/account/above_new_account_form_multiinclude.tpl.php'));?>
	        
	        <div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Username');?></label>
    		  <input <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />
    		</div>
    		
    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password');?></label>
        		<input autocomplete="new-password" type="password" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Password" value="<?php echo htmlspecialchars(isset($user->password_temp_1) ? $user->password_temp_1 : '');?>" />
    		</div>
    		
    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Repeat the new password');?></label>
        		<input autocomplete="new-password" type="password" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Password1" value="<?php echo htmlspecialchars(isset($user->password_temp_2) ? $user->password_temp_2 : '');?>" />
    		</div>
    		
    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','E-mail');?></label>
        		<input type="text" class="form-control" name="Email" value="<?php echo $user->email;?>"/>
    		</div>
    		
    		<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Chat nickname');?></label>
				<input type="text" class="form-control" name="ChatNickname" value="<?php echo htmlspecialchars($user->chat_nickname);?>" />
			</div>
			
    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Name');?></label>
    		  <input type="text" class="form-control" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/>
    		</div>
    		
    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Surname');?></label>
    		  <input type="text" class="form-control" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/>
    		</div>
    		
    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Job title');?></label>
    		  <input type="text" class="form-control" name="JobTitle" value="<?php echo htmlspecialchars($user->job_title);?>"/>
    		</div>
    			    
    		<?php include(erLhcoreClassDesign::designtpl('lhuser/parts/time_zone.tpl.php'));?>
    		
    		<div class="row">
    		
    		  <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/visibility_content.tpl.php'));?>
    		  
    		  <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/after_visibility_content.tpl.php'));?>
    		  
    		  <div class="col-xs-6">
        		  <div class="form-group">
        		      <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User receives other operators permissions request');?>"><input type="checkbox" value="on" name="ReceivePermissionRequest" <?php echo $user->rec_per_req == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User receives other operators permissions request')?></label>
        		  </div>
    		  </div>
    		</div>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/after_permission.tpl.php'));?>
    		
    		<div class="row form-group">
    			<div class="col-md-6">
    				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Skype');?></label>
    				<input type="text" class="form-control" name="Skype" value="<?php echo htmlspecialchars($user->skype);?>"/>
    			</div>
    			<div class="col-md-6">
    				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','XMPP username');?></label>
    				<input type="text" class="form-control" name="XMPPUsername" value="<?php echo htmlspecialchars($user->xmpp_username);?>"/>
    			</div>
    		</div>
    		
    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Photo');?>, (jpg,png)</label>
    		  <input type="file" name="UserPhoto" value="" />
    		</div>
    		
    		<?php if ($user->has_photo) : ?>
    		<div class="form-group">
    			<img src="<?php echo $user->photo_path?>" alt="" width="50" /><br />
    			<label><input type="checkbox" name="DeletePhoto" value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
    		</div>
    		<?php endif;?>
    		
    		<?php if ($can_edit_groups === true) : ?>
    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
        		<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                        'input_name'     => 'DefaultGroup[]',
                        'selected_id'    => $user->user_groups_id,
    					'multiple' 		 => true,
        		        'css_class'       => 'form-control',
                        'list_function'  => 'erLhcoreClassModelGroup::getList',
                        'list_function_params'  => $user_groups_filter
                )); ?>
    		</div>
    		
    		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?>&nbsp;<input type="checkbox" value="on" name="UserDisabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : '' ?> /></label><br>
    		<?php endif; ?>
    		
    		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Do not show user status as online')?>&nbsp;<input type="checkbox" value="on" name="HideMyStatus" <?php echo $user->hide_online == 1 ? 'checked="checked"' : '' ?> /></label><br>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/below_account_edit_multiinclude.tpl.php'));?>
    		
    		<div class="btn-group" role="group" aria-label="...">
        		<input type="submit" class="btn btn-default" name="Save_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Save');?>"/>
    		    <input type="submit" class="btn btn-default" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
    		    <input type="submit" class="btn btn-default" name="Cancel_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Cancel');?>"/>
        	</div>	
	
	   </form>
	</div>
	
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_departments') : ?>active<?php endif;?>" id="departments">
		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Assigned departments');?></h5>
	
		<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>
		
		<?php 
		  $userDepartaments = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id); 
		  $userDepartamentsGroup = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id);
		?>
		
		<form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#departments" method="post" enctype="multipart/form-data">
		
		    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/departments_assignment.tpl.php'));?>
		    
		    <input type="submit" class="btn btn-default" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
		</form> 
    </div>
	
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_pending') : ?>active<?php endif;?>" id="pending">
	   <form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#pending" method="post">

	  	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <div class="form-group">
		    <label><input type="checkbox" name="showAllPendingEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('show_all_pending',1,$user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User can see all pending chats, not only assigned to him');?></label><br>
        </div>

        <div class="form-group">
           <label><input type="checkbox" name="autoAccept" value="1" <?php $user->auto_accept == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Automatically accept assigned chats');?></label>
        </div>

        <div class="form-group">
           <label><input type="checkbox" name="exclude_autoasign" value="1" <?php $user->exclude_autoasign == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assign workflow');?></label>
        </div>

        <div class="form-group">
           <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Maximum active chats');?></label>
           <input type="text" class="form-control" name="maximumChats" value="<?php echo $user->max_active_chats?>" />
        </div>

		<input type="submit" class="btn btn-default" name="UpdatePending_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	   </form>
    </div>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhpermission','see_permissions_users')) : ?>
    <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_permission') : ?>active<?php endif;?>" id="permission">
        <input type="button" class="btn btn-default" name="UpdateSpeech_account" onclick="lhinst.showMyPermissions('<?php echo $user->id?>')" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show permissions');?>" />
		<div id="permissions-summary"></div>		
    </div>
	<?php endif;?>
	
	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/custom_multiinclude_tab.tpl.php'));?>
	
</div>
