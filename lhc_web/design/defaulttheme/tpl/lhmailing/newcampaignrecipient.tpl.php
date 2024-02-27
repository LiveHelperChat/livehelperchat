
<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','New recipient');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailing/newcampaignrecipient')?>/<?php echo $campaign->id?>/<?php if ($item->id > 0) : ?><?php echo $item->id?><?php endif;?>" ng-non-bindable method="post" target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    
    <div class="modal-body">

    <?php if (isset($errors)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

    <?php if (isset($updated) && $updated == true) : ?>
        <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Recipient was created');?>.
        </div>
        <script>
            $('#list-update-import').removeClass('hide');
        </script>
    <?php endif; ?>

    <?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/form_campaign_recipient.tpl.php'));?>

    </div>

    <div class="modal-footer">
        <div class="btn-group" role="group" aria-label="...">
            <input type="submit" class="btn btn-sm btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>
    </div>

</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>