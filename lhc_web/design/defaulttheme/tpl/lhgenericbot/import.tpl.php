<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import bot');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Bot imported'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off" enctype="multipart/form-data">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','File')?> (json)</label>
        <input type="file" name="botfile" value="" />
    </div>

    <input type="submit" name="ImportBot" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import')?>" />

</form>
