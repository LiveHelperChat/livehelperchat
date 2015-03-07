<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/geoadjustment_pre.tpl.php'));?>
<?php if ($system_configuration_links_geoadjustment_enabled == true && $currentUser->hasAccessTo('lhchat','geoadjustment')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoadjustment')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','GEO adjustment');?></a></li>
<?php endif; ?>