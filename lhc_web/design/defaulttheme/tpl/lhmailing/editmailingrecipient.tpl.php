<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Edit recipient');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailing/editmailingrecipient')?>/<?php echo $item->id?>" method="post" ng-non-bindable onsubmit="return lhinst.submitModalForm($(this))">

    <div class="modal-body">
        <?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Updated'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
            <script>
                $('#list-update-import').removeClass('hide');
            </script>
        <?php endif; ?>

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/form_mailing_recipient.tpl.php'));?>
    </div>

    <div class="modal-footer">
        <div class="btn-group" role="group" aria-label="...">
            <input type="submit" class="btn btn-sm btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>
    </div>

</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>