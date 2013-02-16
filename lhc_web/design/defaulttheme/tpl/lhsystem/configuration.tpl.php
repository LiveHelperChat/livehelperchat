<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration');?></h1>

<h4>Chat ralated</h4>
<ul class="circle">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('departament/departaments')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','HTML code');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Blocked users');?></a></li>
</ul>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users and their permissions');?></h4>
<ul class="circle">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/userlist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/grouplist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of groups');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('permission/roles')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of roles');?></a></li>
</ul>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Other');?></h4>
<ul class="circle">
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/expirecache')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Clean cache');?></a></li>
</ul>