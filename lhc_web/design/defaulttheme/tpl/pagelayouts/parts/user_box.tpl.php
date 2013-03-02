<?php
$currentUser = erLhcoreClassUser::instance();    
$UserData = $currentUser->getUserData();
?>
<div class="right pt10">
	<ul class="inline-list">
	   <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account');?> - (<?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?>)</a></li>
	   <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></a></li>
	</ul>
</div>	
	
<?php unset($currentUser);unset($UserData);?>