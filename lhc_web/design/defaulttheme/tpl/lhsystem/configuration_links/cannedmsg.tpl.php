<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/cannedmsg_pre.tpl.php'));?>

<?php if ($system_configuration_links_cannedmsg_enabled == true && $currentUser->hasAccessTo('lhchat','explorecannedmsg')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Canned messages');?></a></li>
<?php endif; ?>

<?php if ($currentUser->hasAccessTo('lhcannedmsg','suggesterconfig')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/suggesterconfiguration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Canned messages suggester configuration');?></a></li>
<?php endif; ?>

<?php if ($currentUser->hasAccessTo('lhcannedmsg','use_replace')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/listreplace')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Replaceable variables');?></a></li>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/cannedmsg_append.tpl.php'));?>
