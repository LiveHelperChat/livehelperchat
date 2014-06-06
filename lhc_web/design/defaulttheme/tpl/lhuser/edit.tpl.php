<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit');?> - <?php echo $user->name,' ',$user->surname?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="section-container auto" data-section data-options="deep_linking: true">
  <section <?php if ($tab == '') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#account"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></p>
    <div class="content" data-section-content data-slug="account">

	<div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Do not enter a password unless you want to change it');?></div>
	<br />

	<form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#account" method="post" autocomplete="off" enctype="multipart/form-data">
	
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Username');?></label>
		<input class="inputfield" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password');?></label>
		<input type="password" class="inputfield" name="Password" value=""/>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Repeat the new password');?></label>
		<input type="password" class="inputfield" name="Password1" value=""/>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','E-mail');?></label>
		<input type="text" class="inputfield" name="Email" value="<?php echo $user->email;?>"/>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Name');?></label>
		<input type="text" class="inputfield" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Surname');?></label>
		<input type="text" class="inputfield" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Job title');?></label>
		<input type="text" name="JobTitle" value="<?php echo htmlspecialchars($user->job_title);?>"/>
			    
		<?php include(erLhcoreClassDesign::designtpl('lhuser/parts/time_zone.tpl.php'));?>
		
		<label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chat status will not change upon pending chat opening');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Invisible mode')?>&nbsp;<input type="checkbox" value="on" name="UserInvisible" <?php echo $user->invisible_mode == 1 ? 'checked="checked"' : '' ?> /></label>
		
		<div class="row">
			<div class="columns small-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Skype');?></label>
				<input type="text" name="Skype" value="<?php echo htmlspecialchars($user->skype);?>"/>
			</div>
			<div class="columns small-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','XMPP username');?></label>
				<input type="text" name="XMPPUsername" value="<?php echo htmlspecialchars($user->xmpp_username);?>"/>
			</div>
		</div>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Photo');?>, (jpg,png)</label>
		<input type="file" name="UserPhoto" value="" />
		
		<?php if ($user->has_photo) : ?>
		<div>
			<img src="<?=$user->photo_path?>" alt="" width="50" /><br />
			<label><input type="checkbox" name="DeletePhoto" value="1" /> <?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
		</div>
		<?php endif;?>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
		<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
		                    'input_name'     => 'DefaultGroup[]',
		                    'selected_id'    => $user->user_groups_id,
							'multiple' 		 => true,
		                    'list_function'  => 'erLhcoreClassModelGroup::getList'
		            )); ?>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?>&nbsp;<input type="checkbox" value="on" name="UserDisabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : '' ?> /></label>
		
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Do not show user status as online')?>&nbsp;<input type="checkbox" value="on" name="HideMyStatus" <?php echo $user->hide_online == 1 ? 'checked="checked"' : '' ?> /></label>
		
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
		
		<ul class="button-group radius">
		<li><input type="submit" class="small button" name="Save_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Save');?>"/></li>
		<li><input type="submit" class="small button" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/></li>
		<li><input type="submit" class="small button" name="Cancel_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Cancel');?>"/></li>
		</ul>
	
	</form>

</div>
</section>

<section <?php if ($tab == 'tab_departments') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#departments"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></p>
    <div class="content" data-section-content data-slug="departments">

		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Assigned departments');?></h5>
	
		<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>
		
		<?php $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($user->id); ?>
		
		<form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#departments" method="post">
		
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
		
		    <label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','All departments')?></label>
		
		    <?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
		        <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>"<?php in_array($departament['id'],$userDepartaments) ? print 'checked="checked"' : '';?>/><?php echo htmlspecialchars($departament['name'])?></label>
		    <?php endforeach; ?>
		    <input type="submit" class="small button" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
		</form>

    </div>
</section>

<section <?php if ($tab == 'tab_pending') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#pending"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Visible pending chats mode');?></a></p>
    <div class="content" data-section-content data-slug="pending">
	   <form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>#pending" method="post">

	  	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<label><input type="checkbox" name="showAllPendingEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('show_all_pending',1,$user->id) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show all pending chats, not only assigned to user');?></label>
		
		<input type="submit" class="small button" name="UpdatePending_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	   </form>
    </div>
</section>

</div>