<?php if ($currentUser->hasAccessTo('lhtranslation','configuration')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('translation/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Automatic translations');?></a></li>
<?php endif;?>