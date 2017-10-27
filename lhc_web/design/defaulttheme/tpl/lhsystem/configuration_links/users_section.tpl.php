<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/users_section_pre.tpl.php'));?>
<?php if ($system_configuration_links_users_section_enabled == true) : ?>
<div class="col-md-6">
  	<?php if ($currentUser->hasAccessTo('lhuser','userlist') || $currentUser->hasAccessTo('lhuser','grouplist') || $currentUser->hasAccessTo('lhpermission','list')) : ?>
	  	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></h4>
		<ul class="circle small-list">
		    <?php if ($currentUser->hasAccessTo('lhuser','userlist')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/userlist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></a></li>
		    <?php endif; ?>
		    
		    <?php if ($currentUser->hasAccessTo('lhuser','userautologin')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/autologinconfig')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Auto login settings');?></a></li>
		    <?php endif; ?>
		    
		    <?php if ($currentUser->hasAccessTo('lhuser','grouplist')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/grouplist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of groups');?></a></li>
		    <?php endif; ?>
		
		    <?php if ($currentUser->hasAccessTo('lhpermission','list')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('permission/roles')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of roles');?></a></li>
		    <?php endif; ?>

		    <?php if ($currentUser->hasAccessTo('lhuser','import')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/import')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Import users');?></a></li>
		    <?php endif; ?>

		</ul>		     
	 <?php endif; ?>
	 	 

	 <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
	 <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Advanced');?></h4>
     <ul class="circle small-list">
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/usersactions')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users actions');?></a></li>
     </ul>
    <?php endif; ?>

 </div>
 <?php endif;?>