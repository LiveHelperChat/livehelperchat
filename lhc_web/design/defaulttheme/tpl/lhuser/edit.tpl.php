<h1 ng-non-bindable><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Edit user');?> - <?php echo htmlspecialchars($user->name_support)?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" role="tablist">
	<li class="nav-item" role="presentation"><a class="nav-link <?php if ($tab == '') : ?>active<?php endif;?>" href="#account" aria-controls="account" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></li>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false) && (
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments_groups') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_individual') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_group')
        )) : ?>
	<li class="nav-item" role="presentation"><a class="nav-link <?php if ($tab == 'tab_departments') : ?>active<?php endif;?>" href="#departments" aria-controls="departments" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></li>
    <?php endif;?>

	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhpermission','see_permissions_users')) : ?>
	<li class="nav-item" role="presentation"><a class="nav-link <?php if ($tab == 'tab_permission') : ?>active<?php endif;?>" href="#permission" aria-controls="permission" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Permissions');?></a></li>
	<?php endif;?>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false)) : ?>
    <li class="nav-item" role="presentation" ><a class="nav-link <?php if ($tab == 'tab_speech') : ?>active<?php endif;?>" href="#speech" aria-controls="speech" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Speech');?></a></li>
    <?php endif; ?>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false)) : ?>
        <li class="nav-item" role="presentation"><a class="nav-link <?php if ($tab == 'tab_pending') : ?>active<?php endif;?>" href="#pending" aria-controls="pending" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chats');?></a></li>
    <?php endif;?>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false)) : ?>
        <li class="nav-item" role="presentation"><a class="nav-link <?php if ($tab == 'tab_notifications') : ?>active<?php endif;?>" href="#notifications" aria-controls="notifications" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Notifications');?></a></li>
    <?php endif;?>

    <?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs/custom_multiinclude_tab.tpl.php'));?>

</ul>

<div class="tab-content" ng-controller="LHCAccountValidator as accval">
	<div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="account">

	   <?php include(erLhcoreClassDesign::designtpl('lhuser/account/above_account_edit_multiinclude.tpl.php'));?>

	   <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Do not enter a password unless you want to change it');?></div>
	   <br />

	   <form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#account" method="post" autocomplete="off" enctype="multipart/form-data">

	        <?php include(erLhcoreClassDesign::designtpl('lhuser/account/above_new_account_form_multiinclude.tpl.php'));?>

	        <div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Username');?>*</label>
    		  <input <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" type="text" ng-non-bindable name="Username" value="<?php echo htmlspecialchars($user->username);?>" />
    		</div>

    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password');?></label>
        		<input ng-non-bindable autocomplete="new-password" type="password" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Password" value="<?php echo htmlspecialchars(isset($user->password_temp_1) ? $user->password_temp_1 : '');?>" />
    		</div>

    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Repeat the new password');?></label>
        		<input ng-non-bindable autocomplete="new-password" type="password" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Password1" value="<?php echo htmlspecialchars(isset($user->password_temp_2) ? $user->password_temp_2 : '');?>" />
    		</div>

           <div class="row">
               <div class="col-6">
                   <div class="form-group">
                       <label><input type="checkbox" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> value="on" name="ForceResetPassword" <?php echo isset($force_reset_password) && $force_reset_password == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Force user to change password on login')?></label>
                   </div>
               </div>
               <div class="col-6">
                   <div class="form-group">
                       <label><input type="checkbox" <?php if (isset($can_edit_groups) && $can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> value="on" name="force_logout" <?php echo $user->force_logout == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Force logout')?></label>
                   </div>
               </div>
           </div>

    		<div class="form-group">
        		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','E-mail');?>*</label>
        		<input type="text" ng-non-bindable <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Email" value="<?php echo $user->email;?>"/>
    		</div>

    		<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Chat nickname');?></label>
				<input type="text" ng-non-bindable <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="ChatNickname" value="<?php echo htmlspecialchars($user->chat_nickname);?>" />
			</div>

    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Name');?>*</label>
    		  <input type="text" ng-non-bindable <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/>
    		</div>

    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Surname');?></label>
    		  <input type="text" ng-non-bindable <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/>
    		</div>

    		<div class="form-group">
    		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Job title');?></label>
    		  <input type="text" ng-non-bindable <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> class="form-control" name="JobTitle" value="<?php echo htmlspecialchars($user->job_title);?>"/>
    		</div>

    		<?php include(erLhcoreClassDesign::designtpl('lhuser/parts/time_zone.tpl.php'));?>

    		<div class="row">

    		  <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/visibility_content.tpl.php'));?>

    		  <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/after_visibility_content.tpl.php'));?>

              <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/hidability.tpl.php'));?>

    		  <div class="col-4">
        		  <div class="form-group">
        		      <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User receives other operators permissions request');?>"><input <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> type="checkbox" value="on" name="ReceivePermissionRequest" <?php echo $user->rec_per_req == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User receives other operators permissions request')?></label>
        		  </div>
    		  </div>
    		</div>

    		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/after_permission.tpl.php'));?>

    		<div class="row form-group">
    			<div class="col-md-6">
    				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Skype');?></label>
    				<input type="text" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> ng-non-bindable class="form-control" name="Skype" value="<?php echo htmlspecialchars($user->skype);?>"/>
    			</div>
    			<div class="col-md-6">
    				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','XMPP username');?></label>
    				<input type="text" <?php if ($can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> ng-non-bindable class="form-control" name="XMPPUsername" value="<?php echo htmlspecialchars($user->xmpp_username);?>"/>
    			</div>
    		</div>

           <div class="row">
               <div class="col-6">
                   <?php if (!($can_edit_groups === false)) : ?>
                       <div class="form-group">
                           <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Photo');?>, (jpg,png)</label>
                           <input type="file" name="UserPhoto" value="" />
                       </div>
                   <?php endif;?>
                   <?php if ($user->has_photo) : ?>
                       <div class="form-group">
                           <img src="<?php echo $user->photo_path?>" alt="" width="50" /><br />
                           <label><input type="checkbox" name="DeletePhoto" value="1" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
                       </div>
                   <?php endif;?>
               </div>
               <div class="col-6">
                   <?php $avatarOptions = ['avatar' => $user->avatar]; ?>
                   <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/avatar_build.tpl.php'));?>
               </div>
           </div>

            <?php if ($can_edit_groups === true) : ?>

            <?php $user_groups_filter['filter']['required'] = 0; if (erLhcoreClassModelGroup::getcount($user_groups_filter) > 0) : ?>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
            <div class="row">
                <?php echo erLhcoreClassRenderHelper::renderCheckbox( array (
                    'input_name'     => 'DefaultGroup[]',
                    'selected_id'    => $user->user_groups_id,
                    'multiple' 		 => true,
                    'css_class'      => 'form-control',
                    'wrap_prepend'   => '<div class="col-3">',
                    'wrap_append'    => '</div>',
                    'list_function'  => 'erLhcoreClassModelGroup::getList',
                    'list_function_params'  => $user_groups_filter,
                    'read_only_list' => $groups_read_only
                )); ?>
            </div>
            <?php endif; ?>

            <?php $user_groups_filter['filter']['required'] = 1; $groupsRequired = erLhcoreClassModelGroup::getList($user_groups_filter); if (!empty($groupsRequired)) : ?>
                     <br/>
                    <label ng-class="{'chat-closed' : !accval.validRequiredGroups}"><i ng-if="!accval.validRequiredGroups" class="material-icons chat-closed">error</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Required groups, choose one or more')?>*</label>
                    <div class="row" ng-init='accval.requiredGroups = <?php $obj = new stdClass(); foreach ($user->user_groups_id as $userGroupId) {if (isset($groupsRequired[$userGroupId])) { $obj->{$userGroupId} = true; }}; echo json_encode($obj, JSON_HEX_APOS)?>;accval.validateGroups()'>
                        <?php echo erLhcoreClassRenderHelper::renderCheckbox( array (
                            'input_name'     => 'DefaultGroup[]',
                            'selected_id'    => $user->user_groups_id,
                            'multiple' 		 => true,
                            'css_class'      => 'form-control',
                            'wrap_prepend'   => '<div class="col-3">',
                            'wrap_append'    => '</div>',
                            'ng_change'      => 'accval.validateGroups()',
                            'ng_model'      => 'accval.requiredGroups[$id]',
                            'list_function'  => 'erLhcoreClassModelGroup::getList',
                            'list_function_params'  => $user_groups_filter
                        )); ?>
                    </div>
            <?php endif; ?>

            <hr>
    		<label><input type="checkbox" value="on" name="UserDisabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : '' ?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?></label><br>
    		<?php endif; ?>

    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/below_account_edit_multiinclude.tpl.php'));?>

    		<div class="btn-group" role="group" aria-label="..." <?php if (empty($groupsRequired)) :?>ng-init="accval.validForm=true"<?php endif?> >

                <?php if (!($can_edit_groups === false)) : ?>
                <input type="submit" class="btn btn-secondary" ng-disabled="!accval.validForm" name="Save_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Save');?>"/>
    		    <input type="submit" class="btn btn-secondary" ng-disabled="!accval.validForm" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
                <?php endif; ?>

    		    <input type="submit" class="btn btn-secondary" name="Cancel_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Cancel');?>"/>
        	</div>

	   </form>
	</div>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false) && (
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments_groups') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_individual') ||
            erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_group')
        )) : ?>
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_departments') : ?>active<?php endif;?>" id="departments">

		<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>

		<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'failed') : $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account update failed! Please try again!'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
		<?php endif; ?>

		<?php
		  $userDepartaments = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id);
		  $userDepartamentsRead = erLhcoreClassUserDep::getUserDepartamentsIndividual($user->id, true);
          $userDepartamentsAutoExc = erLhcoreClassUserDep::getUserDepartamentsExcAutoassignIds($user->id);
          $userDepartamentsParams = erLhcoreClassUserDep::getUserIndividualParams($user->id);

		  $userDepartamentsGroup = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id);
		  $userDepartamentsGroupRead = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds($user->id, true);
          $userDepartamentsGroupAutoExc = erLhcoreClassModelDepartamentGroupUser::getUserGroupsExcAutoassignIds($user->id);
          $userDepartamentsGroupParams = erLhcoreClassModelDepartamentGroupUser::getUserGroupsParams($user->id);

          $departmentEditParams = [
                  'self_edit' => false,
                  'all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','edit_all_departments'),
                  'individual' => [
                          'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments') || erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual'),
                          'edit_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual'),
                          'edit_personal' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_individual'),
                          'all_dep'  => $userDepartamentsParams,
                  ],
                  'groups' => [
                      'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments_groups') || erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group'),
                      'edit_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group'),
                      'edit_personal' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_group'),
                      'all_group' => $userDepartamentsGroupParams
                  ]
          ];

          if ($departmentEditParams['individual']['edit_all'] == false) {
              $departmentEditParams['individual']['id'] = array_merge(
                      erLhcoreClassUserDep::getUserDepartamentsIndividual(
                              erLhcoreClassUser::instance()->getUserID()
                      ),
                      erLhcoreClassUserDep::getUserDepartamentsIndividual(
                              erLhcoreClassUser::instance()->getUserID(),
                              true
                      )
              );
          }

          if ($departmentEditParams['groups']['edit_all'] == false) {
              $departmentEditParams['groups']['id'] = array_merge(
                      erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                              erLhcoreClassUser::instance()->getUserID()
                      ),
                      erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                              erLhcoreClassUser::instance()->getUserID(),
                              true
                      )
              );
          }
		?>

		<form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#departments" method="post" enctype="multipart/form-data">
		    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/departments_assignment.tpl.php'));?>
		</form>
    </div>
    <?php endif; ?>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false)) : ?>
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_pending') : ?>active<?php endif;?>" id="pending">
	   <form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#pending" method="post">

	  	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <div class="form-group">
		    <label><input type="checkbox" name="showAllPendingEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('show_all_pending',1, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User can see all pending chats, not only assigned to him');?></label><br>
        </div>

        <div class="form-group">
           <label><input type="checkbox" name="autoAccept" value="1" <?php $user->auto_accept == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Automatically accept assigned chats');?></label>
        </div>

       <div class="form-group">
           <label><input type="checkbox" name="auto_join_private" value="1" <?php erLhcoreClassModelUserSetting::getSetting('auto_join_private',1, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto join private chats');?></label>
       </div>

       <div class="form-group">
           <label><input type="checkbox" name="no_scroll_bottom" value="1" <?php erLhcoreClassModelUserSetting::getSetting('no_scroll_bottom',0, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not scroll to the bottom on chat open');?></label>
       </div>

       <div class="form-group">
           <fieldset class="border p-2">
               <legend class="w-auto fs16 mb-0"><label class="fs16 m-0 p-0"><input type="checkbox" name="remove_closed_chats" value="1" <?php erLhcoreClassModelUserSetting::getSetting('remove_closed_chats',0, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Remove my closed chats from opened chat list on page refresh');?></label></legend>
               <br>
               <label><input type="checkbox" name="remove_closed_chats_remote" value="1" <?php erLhcoreClassModelUserSetting::getSetting('remove_closed_chats_remote',0, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Include not only my chats');?>
                   <span class="d-block"><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Other operators chats also will be closed on page refresh');?></i></small></span>
               </label>
               <div class="form-group mb-0">
                   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','How much time has to be passed after chat close before chat is removed. Time in minutes.');?></label>
                   <input name="remove_close_timeout" value="<?php echo (int)erLhcoreClassModelUserSetting::getSetting('remove_close_timeout',5, $user->id)?>" class="form-control form-control-sm" type="number" max="60" min="1" >
               </div>
           </fieldset>
       </div>

       <div class="form-group">
           <label><input type="checkbox" name="exclude_autoasign" value="1" <?php $user->exclude_autoasign == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assign workflow');?></label>
       </div>

       <div class="form-group">
           <label><input type="checkbox" name="auto_preload" value="1" <?php erLhcoreClassModelUserSetting::getSetting('auto_preload',0, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto preload previous visitor chat messages');?></label>
       </div>

       <div class="form-group">
           <label><input type="checkbox" name="auto_uppercase" value="1" <?php erLhcoreClassModelUserSetting::getSetting('auto_uppercase',1, $user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto uppercase sentences');?></label>
       </div>

       <div class="form-group">
           <label>
               <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Default number of rows for chat text area');?>
           </label>
           <input class="form-control form-control-sm" type="number" name="chat_text_rows" value="<?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_text_rows',2, $user->id) ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Number of rows');?>">
       </div>

        <div class="form-group">
           <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Maximum active chats');?></label>
           <input type="text" ng-non-bindable class="form-control" name="maximumChats" value="<?php echo $user->max_active_chats?>" />
        </div>

		<input type="submit" class="btn btn-secondary" name="UpdatePending_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	   </form>
    </div>
    <?php endif; ?>

    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhpermission','see_permissions_users')) : ?>
    <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_permission') : ?>active<?php endif;?>" id="permission">
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','In order to change operator permissions you have to edit');?> <a href="<?php echo erLhcoreClassDesign::baseurl('permission/roles')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','roles');?></a>.</p>

        <input type="button" class="btn btn-secondary" name="UpdateSpeech_account" onclick="lhinst.showMyPermissions('<?php echo $user->id?>')" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show permissions');?>" />
		<div id="permissions-summary"></div>
    </div>
	<?php endif;?>

    <?php if (!(isset($can_edit_groups) && $can_edit_groups === false)) : ?>
    <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_speech') : ?>active<?php endif;?>" id="speech">

        <?php if (isset($account_updated) && $account_updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
        <?php endif; ?>

        <form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>" method="post">

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <?php $dataSpeech = array(
                'language' => (int)erLhcoreClassModelUserSetting::getSetting('speech_language','',$user->id),
                'dialect' => (int)erLhcoreClassModelUserSetting::getSetting('speech_dialect','',$user->id),
                'optional' => true,
            ); ?>

            <?php include(erLhcoreClassDesign::designtpl('lhspeech/speech_form_fields.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhspeech/my_languages.tpl.php'));?>

            <input type="submit" class="btn btn-secondary" name="UpdateSpeech_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />

        </form>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/notifications_tab_edit.tpl.php'));?>

    <?php endif; ?>

	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/custom_multiinclude_tab.tpl.php'));?>

</div>
