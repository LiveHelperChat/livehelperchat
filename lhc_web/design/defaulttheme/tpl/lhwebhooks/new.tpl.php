<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/new','New webhook');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('webhooks/new')?>" method="post" enctype="multipart/form-data" ng-controller="WebhooksCtrl as webhooksctl" ng-submit="webhooksctl.updateContinuous()" ng-init='webhooksctl.conditions = <?php echo json_encode($item->conditions_array,JSON_HEX_APOS & JSON_FORCE_OBJECT)?>'>

    <?php include(erLhcoreClassDesign::designtpl('lhwebhooks/form.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
