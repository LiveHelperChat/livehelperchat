<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','New');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/newmatchrule')?>" method="post" ng-controller="WebhooksCtrl as webhooksctl" ng-submit="webhooksctl.updateContinuous()" ng-init='webhooksctl.conditions = <?php echo json_encode($item->conditions_array,JSON_HEX_APOS & JSON_FORCE_OBJECT)?>'>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/form_matchrule.tpl.php'));?>

    <br>
    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>