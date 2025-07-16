<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/edit.tpl.php'));?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($file_uploaded) && $file_uploaded == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <form action="" method="post" enctype="multipart/form-data" ng-non-bindable>
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File name');?></label>
                <p><?php echo htmlspecialchars($item->upload_name)?></p>
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="persistent" value="on" <?php echo $item->persistent == 1 ? print 'checked="checked"' : print ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Persistent');?></label>
                <span class="d-block text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Files maintenance jobs will not be run on this file.');?></span>
            </div>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <input type="submit" class="btn btn-secondary" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Update');?>" />

            <?php if ($item->meta_msg != '') : ?>
                <h6 class="mt-4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Meta data');?></h6>
                <pre class="fs11"><?php echo htmlspecialchars(json_encode($item->meta_msg_array,JSON_PRETTY_PRINT))?></pre>
            <?php endif; ?>

        </form>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','BB code as a file');?></label>
            <input type="text" readonly="readonly" class="form-control form-control-sm" value="[file=<?php echo $item->id,'_',$item->security_hash?>]">
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','BB code as an image');?></label>
            <input type="text" readonly="readonly" class="form-control form-control-sm" value="[file=<?php echo $item->id,'_',$item->security_hash,'_img'?>]">
        </div>
    </div>
</div>

