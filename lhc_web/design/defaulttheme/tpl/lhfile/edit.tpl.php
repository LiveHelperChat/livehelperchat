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
                <label><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File name');?></strong></label>
                <p><?php echo htmlspecialchars($item->upload_name)?></p>
            </div>

            <div class="form-group">
                <label><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list', $item instanceof erLhcoreClassModelMailconvFile ? 'Message' : 'Chat');?> ID</strong></label>
                <p><?php $file_ref_id = $item instanceof erLhcoreClassModelMailconvFile ? $item->message_id : $item->chat_id; echo $file_ref_id > 0 ? htmlspecialchars($file_ref_id) : '-'?></p>
            </div>

            <div class="form-group">
                <label><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Caption');?></strong></label>
                <p><?php echo $item->caption != '' ? htmlspecialchars($item->caption) : '-'?></p>
            </div>

            <div class="form-group">
                <label><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Recognized text (OCR)');?></strong></label>
                <p><?php echo $item->caption_ocr != '' ? nl2br(htmlspecialchars($item->caption_ocr)) : '-'?></p>
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="persistent" value="on" <?php echo $item->persistent == 1 ? print 'checked="checked"' : print ''?> > <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Persistent');?></strong></label>
                <span class="d-block text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Files maintenance jobs will not be run on this file.');?></span>
            </div>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <input type="submit" class="btn btn-secondary" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Update');?>" />

            <?php if ($item->meta_msg != '') : ?>
                <h6 class="mt-4"><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Meta data');?></strong></h6>
                <pre class="fs11"><?php echo htmlspecialchars(json_encode($item->meta_msg_array,JSON_PRETTY_PRINT))?></pre>
            <?php endif; ?>

        </form>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','BB code as a file');?></strong></label>
            <input type="text" readonly="readonly" class="form-control form-control-sm" value="[file=<?php echo $item->id,'_',$item->security_hash?>]">
        </div>
        <div class="form-group">
            <label><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','BB code as an image');?></strong></label>
            <input type="text" readonly="readonly" class="form-control form-control-sm" value="[file=<?php echo $item->id,'_',$item->security_hash,'_img'?>]">
        </div>
    </div>
</div>

