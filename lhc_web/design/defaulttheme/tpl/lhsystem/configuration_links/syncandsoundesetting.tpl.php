<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/syncandsoundesetting_pre.tpl.php'));?>
<?php if ($system_configuration_links_sync_and_sounde_settings_enabled == true) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/syncandsoundesetting')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Synchronization and sound settings');?></a></li>
<?php endif;?>