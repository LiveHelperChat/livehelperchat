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
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ProactiveChatInvitation"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Pro active chat invitations');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/AutoResponder"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Auto responder');?></a></li>
		    <?php endif; ?>

		    <?php if ($currentUser->hasAccessTo('lhsystem','configuresmtp')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/smtp')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','SMTP settings');?></a></li>
		    <?php endif; ?>

		    <?php if ($currentUser->hasAccessTo('lhsystem','configurelanguages') || $currentUser->hasAccessTo('lhsystem','changelanguage')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Languages configuration');?></a></li>
		    <?php endif; ?>

		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chats list');?></a></li>

		    <?php if ($currentUser->hasAccessTo('lhchatarchive','archive')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/archive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat archive');?></a></li>
		    <?php endif; ?>

		</ul>
	</div>

	<div class="columns small-6">
	<?php if ($currentUser->hasAccessTo('lhsystem','generatejs')) : ?>
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Live help embed code');?></h4>
		<ul class="circle small-list">
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
		</ul>
	<?php endif; ?>

	<?php if ($currentUser->hasAccessTo('lhfaq','manage_faq')) : ?>
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','FAQ embed code');?></h4>
		<ul class="circle small-list">
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
		</ul>
	<?php endif; ?>

	<?php if ($currentUser->hasAccessTo('lhquestionary','manage_questionary')) : ?>
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Questionary embed code');?></h4>
		<ul class="circle small-list">
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
		</ul>
	<?php endif; ?>

	<?php if ($currentUser->hasAccessTo('lhchatbox','manage_chatbox')) : ?>
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chatbox embed code');?></h4>
		<ul class="circle small-list">
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
		</ul>
	<?php endif; ?>
	</div>

</div>

<hr>

<?php if ($currentUser->hasAccessTo('lhchat','viewstatistic')) : ?>
	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Statistic');?></h4>
	<ul class="circle small-list">
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/statistic')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Statistic');?></a></li>
	</ul>
	<hr>
<?php endif; ?>


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
<hr>

<?php if ($currentUser->hasAccessTo('lhfile','use') || $currentUser->hasAccessTo('lhfile','file_list')) : ?>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Files uploads configuration');?></h4>
<ul class="circle small-list">
	<?php if ($currentUser->hasAccessTo('lhfile','use')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('file/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Files upload configuration');?></a></li>
    <?php endif; ?>

    <?php if ($currentUser->hasAccessTo('lhfile','file_list')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('file/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of files');?></a></li>
    <?php endif; ?>
</ul>
<hr>
<?php endif; ?>



<?php if ($currentUser->hasAccessTo('lhchat','viewstatistic')) : ?>
	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Statistic');?></h4>
	<ul class="circle small-list">
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/statistic')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Statistic');?></a></li>
	</ul>
<?php endif; ?>




<?php if ($currentUser->hasAccessTo('lhsystem','expirecache')) : ?>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Other');?></h4>
<ul class="circle small-list">
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/expirecache')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Clean cache');?></a></li>
</ul>
<?php endif; ?>