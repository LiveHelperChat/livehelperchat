<?php if ($currentUser->hasAccessTo('lhchat','maintenance') || $currentUser->hasAccessTo('lhchatarchive','archive') || $currentUser->hasAccessTo('lhchatarchive','configuration')) : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat archive');?></h5>
<ul>
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/maintenance.tpl.php'));?>
    <?php if ($currentUser->hasAccessTo('lhchatarchive','archive') || $currentUser->hasAccessTo('lhchatarchive','configuration')) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_archive.tpl.php'));?>
    <?php endif; ?>
</ul>
<?php endif; ?>