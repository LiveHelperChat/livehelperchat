<?php if ($currentUser->hasAccessTo('lhgroupchat','manage') ) : ?>
<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Group chat');?></b>
    <ul>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('groupchat/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Group chats list');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('groupchat/options')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Options');?></a></li>
    </ul>
</li>
<?php endif; ?>