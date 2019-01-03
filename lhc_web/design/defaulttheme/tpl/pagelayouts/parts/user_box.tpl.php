<?php
$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData(true); ?>
<li class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?> <span class="caret"></span></a>
    <div class="dropdown-menu" role="menu">
        <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?>"><i class="material-icons">account_box</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?></a>
        <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?>"><i class="material-icons">exit_to_app</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></a>
    </div>
</li>
<?php unset($currentUser);unset($UserData);?>
