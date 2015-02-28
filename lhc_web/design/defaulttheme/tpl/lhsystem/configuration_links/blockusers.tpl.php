<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/blockusers_pre.tpl.php'));?>
<?php if ($system_configuration_links_blockusers_enabled == true && $currentUser->hasAccessTo('lhchat','allowblockusers')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Blocked users');?></a></li>
<?php endif; ?>