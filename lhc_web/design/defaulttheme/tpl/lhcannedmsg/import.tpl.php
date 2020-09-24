<form action="" method="post" enctype="multipart/form-data">
    
    <?php if (isset($errors)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

    <?php if (isset($update)) : ?>
        <div role="alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Updated');?> - <?php echo $update['updated']?></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Imported');?> - <?php echo $update['imported']?></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Removed');?> - <?php echo $update['removed']?></li>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($update)) : ?>
        <script>
            setTimeout(function(){
                window.parent.location.reload();
            },1500);
        </script>
    <?php endif;?>

    <div class="form-group">
        <label>CSV</label>
        <input type="file" name="files" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="remove_old" value="on" <?php if (isset($remove_old) && $remove_old == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Remove old canned messages');?></label>
        <br/><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','If you do not check we will try to update existing records without removing all records.');?></small>
    </div>

    <input type="submit" class="btn btn-secondary" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Import');?>" />
</form>