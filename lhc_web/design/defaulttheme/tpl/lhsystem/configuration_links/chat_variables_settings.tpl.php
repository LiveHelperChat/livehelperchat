<?php if ($currentUser->hasAccessTo('lhchat','administratechatvariable')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ChatVariable"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Additional chat variables');?></a></li>
<?php endif; ?>