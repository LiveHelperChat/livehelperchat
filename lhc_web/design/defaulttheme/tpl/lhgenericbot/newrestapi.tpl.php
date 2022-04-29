<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New Rest API Call');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<script>
    var rest_api_parameters = <?php isset($item->configuration_array['parameters']) ? print json_encode($item->configuration_array['parameters'],JSON_HEX_APOS) : print "[]"?>;
    var botRestAPIHost = <?php isset($item->configuration_array['host']) ? print json_encode($item->configuration_array['host'], JSON_HEX_APOS) : print "\"\""?>;
    var botRestAPIECache = <?php isset($item->configuration_array['ecache']) && $item->configuration_array['ecache'] == true ? print 'true' : print 'false'?>;
</script>

<form action="<?php echo erLhcoreClassDesign::baseurl('genericbot/newrestapi')?>" method="post" ng-controller="BotRestAPIParameters as lhcrestapi" ng-init='lhcrestapi.initParams();'>

    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/form_rest_api.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
