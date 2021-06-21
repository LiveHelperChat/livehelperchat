<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('lhsystem/singlesetting','Settings');
$modalSize = 'md';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <form action="<?php echo $action_url?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated');?>
             <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
        <?php endif; ?>

        <div class="modal-body">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
        </div>
        <input type="hidden" name="export_action" value="doExport">
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save')?></button>
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>
    </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>