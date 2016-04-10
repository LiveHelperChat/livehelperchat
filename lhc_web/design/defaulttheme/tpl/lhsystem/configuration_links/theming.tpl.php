<?php if ($currentUser->hasAccessTo('lhabstract','use') && $currentUser->hasAccessTo('lhtheme','administratethemes')) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Theming');?></h5>
<ul>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/WidgetTheme"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Widget themes');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/import')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Import new themes');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/default')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Default theme');?></a></li>
</ul>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Back office theming');?></h5>
<ul>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/adminthemes')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Admin themes');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/defaultadmintheme')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Default admin theme');?></a></li>
</ul>
<?php endif; ?>