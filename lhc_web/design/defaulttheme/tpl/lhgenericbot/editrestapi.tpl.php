<h1><?php echo htmlspecialchars($item->name)?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="new-password" enctype="multipart/form-data" ng-controller="BotRestAPIParameters as lhcrestapi" ng-init='lhcrestapi.parameters = <?php isset($item->configuration_array['parameters']) ? print json_encode($item->configuration_array['parameters'],JSON_HEX_APOS) : print "[]"?>;lhcrestapi.host = <?php isset($item->configuration_array['host']) ? print json_encode($item->configuration_array['host'], JSON_HEX_APOS) : print "\"\""?>'>

    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/form_rest_api.tpl.php'));?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
