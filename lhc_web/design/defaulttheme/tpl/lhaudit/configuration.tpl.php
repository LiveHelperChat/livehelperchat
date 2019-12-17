<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Audit Configuration')?></h1>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','How many days keep log?')?></label>
        <input type="text" class="form-control" name="days_log" value="<?php isset($audit_options['days_log']) ? print $audit_options['days_log'] : print '90'?>" />
    </div>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','What objects changes log?')?></h5>
    <?php $objectsLog = array(
        array('class' => 'AutoResponder' ,'name' => 'Auto Responder'),
        array('class' => 'CannedMsg' ,'name' => 'Canned Message'),
        array('class' => 'Subject' ,'name' => 'Subject'),
    );
    ?>

    <div class="row">
    <?php foreach ($objectsLog as $objectToLog) : ?>
        <div class="col-3"><label><input <?php if (isset($audit_options['log_objects']) && in_array($objectToLog['class'],$audit_options['log_objects'])) :?>checked="checked"<?php endif;?> type="checkbox" name="log_objects[]" value="<?php echo $objectToLog['class']?>"><?php echo htmlspecialchars($objectToLog['name'])?></label></div>
    <?php endforeach; ?>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
