<?php
$currentUser = erLhcoreClassUser::instance();
if ($currentUser->isLogged()) :
$UserData = $currentUser->getUserData(true); ?>
<li class="nav-item">
    <a href="#" class="nav-link"><i class="material-icons">&#xf004;</i><span class="nav-link-text"><?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?></span><i class="material-icons arrow">&#xf142</i></a>
    <ul class="nav nav-second-level collapse" role="menu">
        <?php
        $canChangeOnlineStatus = false;
        $currentUser = erLhcoreClassUser::instance();
        if ( $currentUser->hasAccessTo('lhuser','changeonlinestatus') ) {
            $canChangeOnlineStatus = true;
            if ( !isset($UserData) ) {
                $UserData = $currentUser->getUserData(true);
            }
        }

        $canChangeVisibilityMode = false;
        if ( $currentUser->hasAccessTo('lhuser','changevisibility') ) {
            $canChangeVisibilityMode = true;
            if ( !isset($UserData) ) {
                $UserData = $currentUser->getUserData(true);
            }
        }
        ?>

        <?php if ($currentUser->hasAccessTo('lhchat','use') ) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings_sound.tpl.php'));?>

            <?php if ($canChangeVisibilityMode == true) : ?>
                <li class="nav-item"><a href="#" class="nav-link" ng-click="lhc.changeVisibility()"><i id="vi-in-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? '&#xf209' : '&#xf208'}}</i><span class="nav-link-text">Visible/Invisible</span></a></li>
            <?php endif;?>

            <?php if ($canChangeOnlineStatus == true) : ?>
                <li class="nav-item"><a href="#" class="nav-link" ng-click="lhc.changeOnline()"><i id="online-offline-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? '&#xf243' : '&#xf241'}}</i><span class="nav-link-text">Online/Offline</span></a></li>
            <?php endif;?>
        <?php endif;?>

        <li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?>"><i class="material-icons">&#xf004;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?></span></a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?>"><i class="material-icons">&#xf343;</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></span></a></li>

    </ul>
</li>
<?php endif; ?>
