<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions').', '. $update_records . ' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','records will be updated.');
$modalSize = 'lg';
$modalBodyClass = 'p-1';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/?quick_action=1" method="post" target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Updated!') ?>
            </div>
            <script>
                setTimeout(function (){
                    document.location.reload();
                },2000);
            </script>
        <?php else : ?>
            <div class="modal-body">
                <div role="tabpanel" class="tab-pane pt-2" >
                    <div class="row">
                        <div class="col-6">
                            <label><input type="checkbox" name="disable_canned" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Disable')?></label>
                        </div>
                        <div class="col-6">
                            <label><input type="checkbox" name="enable_canned" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Enabled')?></label>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="modal-footer">
            <?php if (!(isset($updated) && $updated == true)) : ?>
                <button type="submit" name="XLS" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save')?></button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>
    </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>