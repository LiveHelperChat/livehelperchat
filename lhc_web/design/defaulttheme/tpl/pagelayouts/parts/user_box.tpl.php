<?php
$currentUser = erLhcoreClassUser::instance();    
$UserData = $currentUser->getUserData();
?>
<div class="right-infobox">
	<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logged user');?></legend>	
	<a href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account');?> - (<?php echo $UserData->name,' ',$UserData->surname?>) &raquo;</a><br /> 
	<a href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?> &raquo;</a>		   	
	</fieldset>
</div>
<?php unset($currentUser);unset($UserData);?>