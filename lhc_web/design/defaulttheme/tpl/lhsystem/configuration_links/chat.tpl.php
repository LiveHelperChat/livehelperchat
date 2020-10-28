<li>
    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat');?></b>
    <ul>
        <?php if ($currentUser->hasAccessTo('lhdepartment','list')) : ?>
            <li><a href="<?php echo erLhcoreClassDesign::baseurl('department/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/subject.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_list.tpl.php'));?>

        <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_configuration.tpl.php'));?>
        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/offline_settings.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/product.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/paidchat.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_column_settings.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/statistic.tpl.php'));?>

    </ul>

</li>