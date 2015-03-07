<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/files_pre.tpl.php'));?>	
<?php if ($system_configuration_links_files_enabled == true && ($currentUser->hasAccessTo('lhfile','use') || $currentUser->hasAccessTo('lhfile','file_list'))) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Files');?></h5>
<ul>
	    <?php if ($currentUser->hasAccessTo('lhfile','use')) : ?>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('file/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Files upload configuration');?></a></li>
	    <?php endif; ?>
	
	    <?php if ($currentUser->hasAccessTo('lhfile','file_list')) : ?>
	    <li><a href="<?php echo erLhcoreClassDesign::baseurl('file/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of files');?></a></li>
	    <?php endif; ?>   
</ul>
<?php endif; ?>