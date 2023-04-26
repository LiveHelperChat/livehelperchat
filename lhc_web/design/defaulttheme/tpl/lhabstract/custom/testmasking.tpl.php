<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Test masking');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <form method="post" action="<?php echo erLhcoreClassDesign::baseurl('abstract/testmasking')?>" onsubmit="return lhinst.submitModalForm($(this))">
                        <div class="row">
                            <div class="col-6">
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Message to test against');?></h6>
                                <textarea name="messages" rows="4" class="form-control form-control-sm"><?php echo htmlspecialchars($messages)?></textarea>
                            </div>
                            <div class="col-6">
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Rules to test');?></h6>
                                <textarea name="mask" rows="4" class="form-control form-control-sm"><?php echo htmlspecialchars($mask)?></textarea>
                            </div>
                            <div class="col-12">
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Output');?></h6>
                                <textarea readonly="readonly" class="form-control form-control-sm"><?php echo htmlspecialchars($output)?></textarea>
                            </div>
                            <div class="col-12 pt-1">
                                <button type="submit" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Test');?></button>
                            </div>
                    </div>
            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
