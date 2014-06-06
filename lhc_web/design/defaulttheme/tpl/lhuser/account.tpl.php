<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Logged user');?> - <?php echo $user->name,' ',$user->surname?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($account_updated) && $account_updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : ?>
		<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>


<div class="section-container auto" data-section data-options="deep_linking: true">
  <section <?php if ($tab == '') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#account"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></p>
    <div class="content" data-section-content data-slug="account">

    <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not enter a password unless you want to change it');?></div>
	<br />
	<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post" autocomplete="off" enctype="multipart/form-data">

		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Username');?></label>
	    <input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Your username');?>" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Password');?></label>
	    <input type="password" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Enter a new password');?>" name="Password" value=""/>

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Repeat password');?></label>
	    <input type="password" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Repeat the new password');?>" name="Password1" value=""/>

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Email');?></label>
	    <input type="text" value="<?php echo $user->email;?>" name="Email" placeholder="Your email address" id="email" class="required email valid">

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Name');?></label>
	    <input type="text" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/>

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Surname');?></label>
	    <input type="text" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/>

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Job title');?></label>
	    <input type="text" name="JobTitle" value="<?php echo htmlspecialchars($user->job_title);?>"/>
    
	    <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/time_zone.tpl.php'));?>
	    
	   	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','changevisibility')) : ?>
		<label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chat status will not change upon pending chat opening');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Invisible mode')?>&nbsp;<input type="checkbox" value="on" name="UserInvisible" <?php echo $user->invisible_mode == 1 ? 'checked="checked"' : '' ?> /></label>
		<?php endif; ?>

	    <div class="row">
	    	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','changeskypenick')) : ?>
			<div class="columns small-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Skype');?></label>
				<input type="text" name="Skype" value="<?php echo htmlspecialchars($user->skype);?>"/>
			</div>
			<?php endif;?>
			<div class="columns small-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','XMPP username');?></label>
				<input type="text" name="XMPPUsername" value="<?php echo htmlspecialchars($user->xmpp_username);?>"/>
			</div>
		</div>
	    
	    

	    
	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Photo');?>, (jpg,png)</label>
		<input type="file" name="UserPhoto" value="" />

		<?php if ($user->has_photo) : ?>
		<div>
			<img src="<?=$user->photo_path?>" alt="" width="50" /><br />
			<label><input type="checkbox" name="DeletePhoto" value="1" /> <?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Delete')?></label>
		</div>
		<?php endif;?>

	    <ul class="button-group radius">
	    <li><input type="submit" name="Update" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"></li>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl()?>" class="small button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Return');?></a></li>
	    </ul>
	</form>

    </div>
  </section>
  
  <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_assigned_departments')) : ?>
  <section <?php if ($tab == 'tab_departments') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#departments"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></p>
    <div class="content" data-section-content data-slug="departments">

	<?php $userDepartaments = erLhcoreClassUserDep::getUserDepartaments(); ?>
	<?php if ($editdepartaments === true) { ?>
	<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>#departments" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','All departments')?></label>
	<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
	    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>" <?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/><?php echo htmlspecialchars($departament['name'])?></label>
	<?php endforeach; ?>
	<input type="submit" class="small button" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	</form>
	<?php } else {?>
	<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
	    <label><input type="checkbox" disabled value="<?php echo $departament['id']?>"<?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?php echo htmlspecialchars($departament['name'])?></label>
	<?php endforeach; ?>

	<?php } ?>
    </div>
  </section>
  <?php endif; ?>
   
   
  <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','change_visibility_list')) : ?>
  <section <?php if ($tab == 'tab_settings') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title ><a href="#lists"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Visible lists');?></a></p>
    <div class="content" data-section-content data-slug="lists">

	  <form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post">

	  	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<label><input type="checkbox" name="pendingTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Pending chats tab enabled');?></label>
		<label><input type="checkbox" name="activeTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_active_list',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Active chats tab enabled');?></label>
		<label><input type="checkbox" name="unreadTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Unread chats tab enabled');?></label>
		<label><input type="checkbox" name="closedTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_close_list',0) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Closed chats tab enabled');?></label>
		<input type="submit" class="small button" name="UpdateTabsSettings_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	  </form>

    </div>
  </section>
  <?php endif; ?>
  
  <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalcannedmsg')) : ?>
  <section <?php if ($tab == 'tab_canned') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#canned"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Personal canned messages');?></a></p>
    <div class="content" data-section-content data-slug="canned">
	  <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/canned_messages.tpl.php'));?>
    </div>
  </section>
  <?php endif; ?>
  
  <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','allowtochoosependingmode')) : ?>
  <section <?php if ($tab == 'tab_pending') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#pending"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Visible pending chats mode');?></a></p>
    <div class="content" data-section-content data-slug="pending">
	   <form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post">

	  	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<label><input type="checkbox" name="showAllPendingEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('show_all_pending',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Show all pending chats, not only assigned to me');?></label>
		
		<input type="submit" class="small button" name="UpdatePending_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	   </form>
	  
    </div>
  </section>
  <?php endif; ?>
  
  
  
  
</div>