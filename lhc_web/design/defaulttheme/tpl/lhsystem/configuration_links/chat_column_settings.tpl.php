<?php if ($currentUser->hasAccessTo('lhchat','administratecolumn')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ChatColumn"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Additional chat columns');?></a></li>
<?php endif; ?>