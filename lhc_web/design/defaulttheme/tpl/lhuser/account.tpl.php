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


<div class="section-container auto" data-section>
  <section <?php if ($tab == '') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account data');?></a></p>
    <div class="content" data-section-content>

    <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not enter a password unless you want to change it');?></div>
	<br />
	<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post" autocomplete="off">

	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Username');?></label>
	    <input type="text" placeholder="Your username" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />

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

	    <ul class="button-group radius">
	    <li><input type="submit" name="Update" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"></li>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl()?>" class="small button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Return');?></a></li>
	    </ul>
	</form>

    </div>
  </section>
  <section <?php if ($tab == 'tab_departments') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></a></p>
    <div class="content" data-section-content>

	<?php $userDepartaments = erLhcoreClassUserDep::getUserDepartaments(); ?>
	<?php if ($editdepartaments === true) { ?>
	<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post">
	<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> />All departments</label>
	<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
	    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>" <?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/><?php echo $departament['name']?></label>
	<?php endforeach; ?>
	<input type="submit" class="small button" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	</form>
	<?php } else {?>
	<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
	    <label><input type="checkbox" disabled value="<?php echo $departament['id']?>"<?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?php echo $departament['name']?></label>
	<?php endforeach; ?>

	<?php } ?>
    </div>
  </section>
  <section <?php if ($tab == 'tab_settings') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Visible lists');?></a></p>
    <div class="content" data-section-content>

	  <form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post">
		<label><input type="checkbox" name="pendingTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Pending chats tab enabled');?></label>
		<label><input type="checkbox" name="activeTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_active_list',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Active chats tab enabled');?></label>
		<label><input type="checkbox" name="unreadTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Unread chats tab enabled');?></label>
		<label><input type="checkbox" name="closedTabEnabled" value="1" <?php erLhcoreClassModelUserSetting::getSetting('enable_close_list',0) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Closed chats tab enabled');?></label>
		<input type="submit" class="small button" name="UpdateTabsSettings_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
	  </form>

    </div>
  </section>
</div>