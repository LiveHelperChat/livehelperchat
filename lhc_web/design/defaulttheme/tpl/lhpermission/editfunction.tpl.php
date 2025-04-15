<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Edit function');?></h1>

<?php if (isset($updated) && $updated == true) : ?>
    <?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<ul ng-non-bindable>
    <li><b><?php echo htmlspecialchars(erLhcoreClassModules::getModuleName($function->module))?> (<?php echo $function->module?>)</b></li>
    <li><b><?php echo htmlspecialchars(erLhcoreClassModules::getFunctionName($function->module,$function->function))?> (<?php echo $function->function?>)</b></li>
</ul>

<form action="" method="post" ng-non-bindable>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <label><input type="checkbox" value="1" name="type" <?php if ($function->type == 1) : ?>checked="checked"<?php endif;?> >&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Exclude permission. Operator will lose access to this function.')?></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Limitation')?></label>
        <textarea name="Limitation" class="form-control"><?php echo htmlspecialchars($function->limitation)?></textarea>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>
</form>