<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/new','New incoming webhook');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('webhooks/newincoming')?>" method="post" enctype="multipart/form-data" ng-controller="WebhooksIncomingCtrl as webhookincomingsctl" ng-submit="webhookincomingsctl.updateContinuous()" ng-init='webhookincomingsctl.conditions = <?php echo json_encode($item->conditions_array,JSON_HEX_APOS)?>'>

    <?php include(erLhcoreClassDesign::designtpl('lhwebhooks/form_incoming.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
