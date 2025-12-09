<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = $item->id > 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Edit manual import') : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Create manual import');
$modalSize = 'md';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/manualimport')?><?php if ($item->id > 0) : ?>/(id)/<?php echo $item->id?><?php endif;?>" ng-non-bindable method="post" target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    
    <div class="modal-body">

    <?php if (isset($errors)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

    <?php if (isset($updated) && $updated == true) : ?>
        <div role="alert" class="alert alert-info alert-dismissible fade show">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Manual import record was created');?>.
        </div>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        </script>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailbox');?></label>
        <select class="form-control" name="mailbox_id" required>
          <?php foreach ($mailboxes as $mailbox) : ?>
            <option value="<?php echo $mailbox->id ?>" <?php if ($item->mailbox_id == $mailbox->id) : ?>selected="selected"<?php endif; ?>><?php echo htmlspecialchars($mailbox->name ?: $mailbox->mail) ?></option>
          <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','UID');?></label>
        <input type="number" class="form-control" name="uid" required value="<?php echo (int)$item->uid ?>">
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Status');?></label>
        <select class="form-control" name="status">
            <option value="0" <?php if ($item->status == 0) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Pending');?></option>
            <option value="1" <?php if ($item->status == 1) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Ignore');?></option>
        </select>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Attempt');?></label>
        <input type="number" class="form-control" name="attempt" min="0" value="<?php echo (int)$item->attempt ?>">
    </div>

    <?php if ($item->id > 0 && $item->last_failure != '') : ?>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Last failure');?></label>
        <pre class="bg-light p-2" style="max-height:200px;overflow:auto;"><?php echo htmlspecialchars($item->last_failure)?></pre>
    </div>
    <?php endif; ?>

    </div>

    <div class="modal-footer">
        <div class="btn-group" role="group" aria-label="...">
            <input type="submit" class="btn btn-sm btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>
    </div>

</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
