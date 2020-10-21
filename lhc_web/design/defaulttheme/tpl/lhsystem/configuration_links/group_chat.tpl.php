<?php if ($currentUser->hasAccessTo('lhgroupchat','manage') ) : ?>
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Group chat');?></h5>
    <ul>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('groupchat/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Group chats list');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('groupchat/options')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Options');?></a></li>
    </ul>
<?php endif; ?>