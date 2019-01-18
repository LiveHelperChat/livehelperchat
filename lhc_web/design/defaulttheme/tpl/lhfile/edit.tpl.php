<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/edit.tpl.php'));?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($file_uploaded) && $file_uploaded == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File name');?></label>
        <p><?php echo htmlspecialchars($item->upload_name)?></p>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="persistent" value="on" <?php echo $item->persistent == 1 ? print 'checked="checked"' : print ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Persistent');?></label>
    </div>

    <input type="submit" class="btn btn-secondary" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Upload');?>" />
</form>