<?php
$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData(true); ?>
<div class="pt10 float-break">
	<ul class="inline-list small-list user-account-list mb0">
	   <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account');?> - (<?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?>)</a></li>
	   <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></a></li>
	</ul>
</div>
<?php unset($currentUser);unset($UserData);?>