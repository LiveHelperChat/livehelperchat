<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/configuration','Chatbox');?></h1>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/configuration','General');?></h4>
<ul class="circle small-list">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/generalsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/configuration','General settings');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/configuration','Chatbox list');?></a></li>
</ul>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/configuration','Embed code generation');?></h4>
<ul class="circle small-list">
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
</ul>