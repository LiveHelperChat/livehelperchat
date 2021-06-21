<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Automation');?></b>
    <ul>
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_variables.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_events.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_campaign.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/autoresponder.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/cannedmsg.tpl.php'));?>

    <?php if ($currentUser->hasAccessTo('lhsystem','transferconfiguration')) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/transfer_configuration.tpl.php'));?>
    <?php endif; ?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/translation.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_priority_settings.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_variables_settings.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/alert_icons.tpl.php'));?>
    </ul>
</li>