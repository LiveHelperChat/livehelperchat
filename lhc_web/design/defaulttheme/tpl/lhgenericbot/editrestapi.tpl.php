<h1 ng-non-bindable><?php echo htmlspecialchars($item->name)?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<script>
    window.rest_api_parameters = <?php isset($item->configuration_array['parameters']) ? print json_encode($item->configuration_array['parameters'],JSON_HEX_APOS) : print "[]"?>;
    window.botRestAPIHost = <?php isset($item->configuration_array['host']) ? print json_encode($item->configuration_array['host'], JSON_HEX_APOS) : print "\"\""?>;
    window.botRestAPIECache = <?php isset($item->configuration_array['ecache']) && $item->configuration_array['ecache'] == true ? print 'true' : print 'false'?>;
    window.botRestAPIAuditLog = <?php isset($item->configuration_array['log_audit']) && $item->configuration_array['log_audit'] == true ? print 'true' : print 'false'?>;
    window.botRestAPISystemLog = <?php isset($item->configuration_array['log_system']) && $item->configuration_array['log_system'] == true ? print 'true' : print 'false'?>;
    window.botRestAPICode = <?php isset($item->configuration_array['log_code']) ?  print json_encode($item->configuration_array['log_code'],JSON_HEX_APOS) : print "\"\""?>;
</script>

<form action="" method="post" autocomplete="new-password" enctype="multipart/form-data" ng-controller="BotRestAPIParameters as lhcrestapi" ng-init='lhcrestapi.initParams();'>

    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/form_rest_api.tpl.php'));?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
