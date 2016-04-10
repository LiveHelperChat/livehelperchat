<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Theme');?></h1>

<ul>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/WidgetTheme"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Widget themes');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/import')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Import a new theme');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/default')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Default theme');?></a></li>
</ul>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Admin');?></h5>
<ul>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/adminthemes')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Admin themes');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('theme/defaultadmintheme')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Default admin theme');?></a></li>
</ul>