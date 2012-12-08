<?php
$currentUser = erLhcoreClassUser::instance();    
$UserData = $currentUser->getUserData();
?>
<div class="right-infobox">
	<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logged user');?></legend>	
	<a href="<?=erLhcoreClassDesign::baseurl('/user/account/')?>"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account');?> - (<?echo $UserData->name,' ',$UserData->surname?>) &raquo;</a><br /> 
	<a href="<?=erLhcoreClassDesign::baseurl('/user/logout/')?>"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?> &raquo;</a>		   	
	</fieldset>
</div>
<? unset($currentUser);unset($UserData);?>