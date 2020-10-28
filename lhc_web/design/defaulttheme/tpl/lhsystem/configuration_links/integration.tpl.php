<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Integration');?></b>
    <ul>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/xmpp.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/restapi.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/ga.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/webhooks.tpl.php'));?>
    </ul>
</li>