<?php if ($currentUser->hasAccessTo('lhchat','administratesubject')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Subject"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Subjects');?></a></li>
<?php endif; ?>