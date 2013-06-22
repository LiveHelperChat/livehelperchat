<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration');?></h1>

<?php $currentUser = erLhcoreClassUser::instance(); ?>

<div class="row">
<div class="columns small-6">
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat related');?></h4>
<ul class="circle small-list">
    <?php if ($currentUser->hasAccessTo('lhdepartament','list')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('departament/departaments')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
    <?php endif; ?>

    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/notificationsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','New chat notification settings');?></a></li>

    <?php if ($currentUser->hasAccessTo('lhchat','allowblockusers')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Blocked users');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/listchatconfig')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat configuration');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','GEO detection configuration');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/syncandsoundesetting')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Synchronization and sound settings');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/startchatformsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Start chat form settings');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhchat','administratecannedmsg')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Canned messages');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/EmailTemplate"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','E-mail templates');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhsystem','configuresmtp')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/smtp')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','SMTP settings');?></a></li>
    <?php endif; ?>

</ul>
</div>


<?php if ($currentUser->hasAccessTo('lhsystem','generatejs')) : ?>
<div class="columns small-6">
	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat embed code');?></h4>
	<ul class="circle small-list">
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
	</ul>
</div>
<?php endif; ?>

</div>

<hr>



<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users and their permissions');?></h4>
<ul class="circle small-list">
    <?php if ($currentUser->hasAccessTo('lhuser','userlist')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/userlist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhuser','grouplist')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/grouplist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of groups');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhpermission','list')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('permission/roles')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of roles');?></a></li>
    <?php endif; ?>

</ul>
<?php if ($currentUser->hasAccessTo('lhsystem','expirecache')) : ?>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Other');?></h4>
<ul class="circle small-list">
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/expirecache')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Clean cache');?></a></li>
</ul>
<?php endif; ?>