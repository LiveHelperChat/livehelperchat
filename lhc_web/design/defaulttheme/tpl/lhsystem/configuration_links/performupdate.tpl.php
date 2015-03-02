<?php if ($currentUser->hasAccessTo('lhsystem','performupdate')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/update')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Update information');?></a></li>
<?php endif; ?>