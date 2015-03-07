<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/autoresponder_pre.tpl.php'));?>
<?php if ($system_configuration_links_autoresponder_enabled == true && $currentUser->hasAccessTo('lhchat','administrateresponder')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/AutoResponder"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Auto responder');?></a></li>
<?php endif;?>