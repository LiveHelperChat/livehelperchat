<?php if ($currentUser->hasAccessTo('lhchat','maintenance') || $currentUser->hasAccessTo('lhchatarchive','archive') || $currentUser->hasAccessTo('lhchatarchive','configuration')) : ?>
<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat archive');?></b>
    <ul>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/maintenance.tpl.php'));?>
        <?php if ($currentUser->hasAccessTo('lhchatarchive','archive') || $currentUser->hasAccessTo('lhchatarchive','configuration')) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_archive.tpl.php'));?>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>