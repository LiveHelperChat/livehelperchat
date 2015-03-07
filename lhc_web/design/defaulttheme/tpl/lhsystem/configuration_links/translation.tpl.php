<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/translation_pre.tpl.php'));?>	
<?php if ($system_configuration_links_translation_enabled == true && $currentUser->hasAccessTo('lhtranslation','configuration')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('translation/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Automatic translations');?></a></li>
<?php endif;?>