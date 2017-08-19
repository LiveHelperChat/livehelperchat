<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhuser/pre_user_form.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/new')?>" method="post" autocomplete="off" enctype="multipart/form-data">

<ul class="nav nav-pills" role="tablist">
	<li role="presentation" <?php if ($tab == '') : ?> class="active" <?php endif;?>><a href="#account" aria-controls="account" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></li>
	<li role="presentation" <?php if ($tab == 'tab_departments') : ?>class="active"<?php endif;?>><a href="#departments" aria-controls="departments" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></li>
	<li role="presentation" <?php if ($tab == 'tab_pending') : ?>class="active"<?php endif;?>><a href="#pending" aria-controls="pending" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Pending chats');?></a></li>
	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs/custom_multiinclude_tab.tpl.php'));?>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="account">
	    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/above_account_new_multiinclude.tpl.php'));?>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Username');?></label>
		  <input class="form-control" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','E-mail');?></label>
		  <input type="text" class="form-control" name="Email" value="<?php echo htmlspecialchars($user->email);?>"/>
		</div>
					
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Password');?></label>
		  <input type="password" class="form-control" autocomplete="new-password" name="Password" value="<?php echo htmlspecialchars(isset($user->password_temp_1) ? $user->password_temp_1 : '');?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Repeat the new password');?></label>
		  <input type="password" class="form-control" autocomplete="new-password" name="Password1" value="<?php echo htmlspecialchars(isset($user->password_temp_2) ? $user->password_temp_2 : '');?>" />
		</div>
		
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Chat nickname');?></label>
			<input type="text" class="form-control" name="ChatNickname" value="<?php echo htmlspecialchars($user->chat_nickname);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Name');?></label>
		  <input class="form-control" type="text" name="Name" value="<?php echo htmlspecialchars($user->name);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Surname');?></label>
		  <input class="form-control" type="text" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>" />
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
				<input class="form-control" type="text" name="Skype" value="<?php echo htmlspecialchars($user->skype);?>"/>
			</div>
			<div class="col-md-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','XMPP username');?></label>
				<input class="form-control" type="text" name="XMPPUsername" value="<?php echo htmlspecialchars($user->xmpp_username);?>"/>
			</div>
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Photo');?>, (jpg,png)</label>
		  <input type="file" name="UserPhoto" value="" />
		</div>
		
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
		
		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/hide_online.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/below_new_account_multiinclude.tpl.php'));?>
		
		<input type="submit" class="btn btn-default" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>" />
	</div>
	
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_departments') : ?>active<?php endif;?>" id="departments">
	    
	    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/departments_assignment.tpl.php'));?>
	    		
		<input type="submit" class="btn btn-default" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>" />
	</div>
	
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_pending') : ?>active<?php endif;?>" id="pending">
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<label><input type="checkbox" name="showAllPendingEnabled" value="1" <?php $show_all_pending == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User can see all pending chats, not only assigned to him');?></label><br>

        <div class="form-group">
            <label><input type="checkbox" name="autoAccept" value="1" <?php $user->auto_accept == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Automatically accept assigned chats');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Maximum active chats');?></label>
            <input type="text" class="form-control" name="maximumChats" value="<?php echo $user->max_active_chats?>" />
        </div>

 		<input type="submit" class="btn btn-default" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>" />
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/custom_multiinclude_tab.tpl.php'));?>
	
</div>
</form>

