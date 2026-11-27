<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_embed_pre.tpl.php'));?>
<?php if ($system_configuration_links_chat_embed_enabled == true && $currentUser->hasAccessTo('lhsystem','generatejs')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcodebeta')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Embed code');?></a></li>
<?php endif; ?>