<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/configuresmtp_pre.tpl.php'));?>	
<?php if ($system_configuration_links_configuresmtp_enabled == true && $currentUser->hasAccessTo('lhsystem','configuresmtp')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/smtp')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Mail settings');?></a></li>
<?php endif; ?>