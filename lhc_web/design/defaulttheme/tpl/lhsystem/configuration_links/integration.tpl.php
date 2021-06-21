<?php if ($currentUser->hasAccessTo('lhxmp','configurexmp') ||
    $currentUser->hasAccessTo('lhrestapi','use_admin') ||
    $currentUser->hasAccessTo('lhsystem','ga_configuration') ||
    $currentUser->hasAccessTo('lhwebhooks','configuration')
) : ?>
<li class="empty-settings">
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Integration');?></b>
    <ul>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/xmpp.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/restapi.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/ga.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/webhooks.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/webhooks_incoming.tpl.php'));?>
    </ul>
</li>
<?php endif; ?>