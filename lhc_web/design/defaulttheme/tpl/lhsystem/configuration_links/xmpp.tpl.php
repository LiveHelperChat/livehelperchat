<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/xmpp_pre.tpl.php'));?>
<?php if ($system_configuration_links_xmpp_enabled == true && $currentUser->hasAccessTo('lhxmp','configurexmp')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('xmp/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','XMPP settings');?></a></li>
<?php endif; ?>