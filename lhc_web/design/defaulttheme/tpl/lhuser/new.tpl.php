<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhuser/pre_user_form.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/new')?>" method="post" autocomplete="off" enctype="multipart/form-data">

<ul class="nav nav-tabs mb-3" role="tablist">
	<li role="presentation" class="nav-item"><a class="nav-link <?php if ($tab == '') : ?>active<?php endif;?>" href="#account" aria-controls="account" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></li>
	<li role="presentation" class="nav-item"><a class="nav-link <?php if ($tab == 'tab_departments') : ?>active<?php endif;?>" href="#departments" aria-controls="departments" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></li>
	<li role="presentation" class="nav-item"><a class="nav-link <?php if ($tab == 'tab_pending') : ?>active<?php endif;?>" href="#pending" aria-controls="pending" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chats');?></a></li>
	<li role="presentation" class="nav-item"><a class="nav-link <?php if ($tab == 'tab_notifications') : ?>active<?php endif;?>" href="#notifications" aria-controls="notifications" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Notifications');?></a></li>
	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs/custom_multiinclude_tab.tpl.php'));?>
</ul>

<div class="tab-content" ng-controller="LHCAccountValidator as accval">
	<div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="account">
	    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/above_account_new_multiinclude.tpl.php'));?>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Username');?>*</label>
		  <input class="form-control" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','E-mail');?>*</label>
		  <input type="text" ng-non-bindable class="form-control" name="Email" value="<?php echo htmlspecialchars($user->email);?>"/>
		</div>
					
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Password');?>*</label>
		  <input type="password" ng-non-bindable class="form-control" autocomplete="new-password" name="Password" value="<?php echo htmlspecialchars(isset($user->password_temp_1) ? $user->password_temp_1 : '');?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Repeat the new password');?>*</label>
		  <input type="password" ng-non-bindable class="form-control" autocomplete="new-password" name="Password1" value="<?php echo htmlspecialchars(isset($user->password_temp_2) ? $user->password_temp_2 : '');?>" />
		</div>

        <div class="form-group">
            <label><input type="checkbox" value="on" name="ForceResetPassword" <?php echo isset($_POST['ForceResetPassword']) ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Force user to change password on login')?></label>
        </div>

		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Chat nickname');?></label>
			<input type="text" class="form-control" ng-non-bindable name="ChatNickname" value="<?php echo htmlspecialchars($user->chat_nickname);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Name');?>*</label>
		  <input class="form-control" ng-non-bindable type="text" name="Name" value="<?php echo htmlspecialchars($user->name);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Surname');?></label>
		  <input class="form-control" ng-non-bindable type="text" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>" />
		</div>
		
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Job title');?></label>
		  <input type="text" ng-non-bindable class="form-control" name="JobTitle" value="<?php echo htmlspecialchars($user->job_title);?>"/>
		</div>

        <?php
        $timeZoneSettings = [
                'force_choose' => true
        ];
        ?>
		<?php include(erLhcoreClassDesign::designtpl('lhuser/parts/time_zone.tpl.php'));?>
		
		<div class="row">
		  
		  <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/visibility_content.tpl.php'));?>
    		  
          <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/after_visibility_content.tpl.php'));?>

          <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/hidability.tpl.php'));?>

		  <div class="col-4">
    		  <div class="form-group">
    		      <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User receives other operators permissions request');?>"><input type="checkbox" value="on" name="ReceivePermissionRequest" <?php echo $user->rec_per_req == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User receives other operators permissions request')?></label>
    		  </div>
		  </div>
		</div>
		
		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/after_permission.tpl.php'));?>
		
		<div class="row form-group">
			<div class="col-md-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Skype');?></label>
				<input class="form-control" ng-non-bindable maxlength="50" type="text" name="Skype" value="<?php echo htmlspecialchars($user->skype);?>"/>
			</div>
			<div class="col-md-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','XMPP username');?></label>
				<input class="form-control" ng-non-bindable type="text" name="XMPPUsername" value="<?php echo htmlspecialchars($user->xmpp_username);?>"/>
			</div>
		</div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Photo');?>, (jpg,png)</label>
                    <input type="file" name="UserPhoto" value="" />
                </div>
            </div>
            <div class="col-6">
                <?php $avatarOptions = ['avatar' => $user->avatar]; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/avatar_build.tpl.php'));?>
            </div>
        </div>

        <?php $user_groups_filter['filter']['required'] = 0; if (erLhcoreClassModelGroup::getcount($user_groups_filter) > 0) : ?>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
            <div class="row">
                <?php echo erLhcoreClassRenderHelper::renderCheckbox( array (
                    'input_name'     => 'DefaultGroup[]',
                    'selected_id'    => $user->user_groups_id,
                    'multiple'       => true,
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
            <div class="row" ng-init='accval.requiredGroups = <?php $obj = new stdClass(); foreach ($user->user_groups_id as $userGroupId) {if (isset($groupsRequired[$userGroupId])) { $obj->{$userGroupId} = true; }}; echo json_encode($obj,JSON_HEX_APOS)?>;accval.validateGroups()'>
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
		<label>&nbsp;<input type="checkbox" value="on" name="UserDisabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : '' ?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?></label><br>
						
		<?php include(erLhcoreClassDesign::designtpl('lhuser/account/below_new_account_multiinclude.tpl.php'));?>

        <div class="btn-group mt-2" role="group" aria-label="Basic example">
		    <input <?php if (empty($groupsRequired)) :?>ng-init="accval.validForm=true"<?php endif?> type="submit" class="btn btn-sm btn-secondary" ng-disabled="!accval.validForm" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>" />
		    <input <?php if (empty($groupsRequired)) :?>ng-init="accval.validForm=true"<?php endif?> type="submit" class="btn btn-sm btn-secondary" ng-disabled="!accval.validForm" name="Update_account_edit" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save and edit');?>" />
        </div>

	</div>
	
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_departments') : ?>active<?php endif;?>" id="departments">
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Departments you will be able to assign once you save an operator.')?></p>
        <?php /*
            $departmentEditParams = [
                'self_edit' => false,
                'edit_all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','edit_all_departments'),
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
	    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/departments_assignment.tpl.php'));?>
	    		
		<input type="submit" class="btn btn-secondary" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>" />*/ ?>
	</div>
	
	<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_pending') : ?>active<?php endif;?>" id="pending">
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <div class="form-group">
            <label><input type="checkbox" name="showAllPendingEnabled" value="1" <?php $quick_settings['show_all_pending'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User can see all pending chats, not only assigned to him');?></label><br>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="autoAccept" value="1" <?php $user->auto_accept == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Automatically accept assigned chats');?></label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="auto_join_private" value="1" <?php $quick_settings['auto_join_private'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto join private chats');?></label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="no_scroll_bottom" value="1" <?php $quick_settings['no_scroll_bottom'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not scroll to the bottom on chat open');?></label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="remove_closed_chats" value="1" <?php $quick_settings['remove_closed_chats'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Remove my closed chats from opened chat list on page refresh');?></label>
        </div>

        <div class="form-group">
            <fieldset class="border p-2">
                <legend class="w-auto fs16 mb-0"><label class="fs16 m-0 p-0"><input type="checkbox" name="remove_closed_chats" value="1" <?php $quick_settings['remove_closed_chats'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Remove my closed chats from opened chat list on page refresh');?></label></legend>
                <br>
                <label><input type="checkbox" name="remove_closed_chats_remote" value="1" <?php $quick_settings['remove_closed_chats_remote'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Include not only my chats');?>
                    <span class="d-block"><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Other operators chats also will be closed on page refresh');?></i></small></span>
                </label>
                <div class="form-group mb-0">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','How much time has to be passed after chat close before chat is removed. Time in minutes.');?></label>
                    <input name="remove_close_timeout" value="<?php echo (int)$quick_settings['remove_close_timeout']?>" class="form-control form-control-sm" type="number" max="60" min="1" >
                </div>
            </fieldset>
        </div>



        <div class="form-group">
            <label><input type="checkbox" name="exclude_autoasign" value="1" <?php $user->exclude_autoasign == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Exclude from auto assign workflow');?></label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="auto_preload" value="1" <?php $quick_settings['auto_preload']== 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto preload previous visitor chat messages');?></label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="auto_uppercase" value="1" <?php $quick_settings['auto_uppercase'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto uppercase sentences');?></label>
        </div>

        <div class="form-group">
            <label>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Default number of rows for chat text area');?>
            </label>
            <input class="form-control form-control-sm" type="number" name="chat_text_rows" value="<?php echo (int)$quick_settings['chat_text_rows'] ?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Number of rows');?>">
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Maximum active chats');?></label>
            <input type="text" class="form-control" name="maximumChats" value="<?php echo $user->max_active_chats?>" />
        </div>

        <input type="submit" class="btn btn-sm btn-secondary" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>" />
	</div>

    <?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/notifications_tab_new.tpl.php'));?>
	
	<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/custom_multiinclude_tab.tpl.php'));?>
	
</div>
</form>

