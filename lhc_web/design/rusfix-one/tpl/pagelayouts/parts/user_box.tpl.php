<?php
$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData(true); ?>
<li class="divider"></li>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?>"> <?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?></a></li>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?>"><i class="icon-logout"></i></a></li>
<?php unset($currentUser);unset($UserData);?>