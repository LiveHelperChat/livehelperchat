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
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Chat ID');?></h6>
                                <input type="number" name="chat_id" min="0" value="<?php echo (int)$chat_id?>" class="form-control form-control-sm" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Optional');?>" />
                                <small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Department, assigned operator and its permissions are taken from the chat. Leave empty to test a pattern directly.');?></small>
                            </div>
                            <div class="col-6">
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Message to test against');?></h6>
                                <textarea name="messages" rows="4" class="form-control form-control-sm"><?php echo htmlspecialchars($messages)?></textarea>
                                <small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Treated as a visitor message (user_id 0).');?></small>
                            </div>
                            <div class="col-6">
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Rules to test');?></h6>
                                <textarea name="mask" rows="4" class="form-control form-control-sm"><?php echo htmlspecialchars($mask)?></textarea>
                                <small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Used only when no chat is provided.');?></small>
                            </div>
                            <div class="col-6">
                                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Output');?></h6>
                                <textarea readonly="readonly" rows="4" class="form-control form-control-sm"><?php echo htmlspecialchars($output)?></textarea>
                            </div>
                            <div class="col-12 pt-1">
                                <button type="submit" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Test');?></button>
                            </div>
                    </div>
            </form>

<?php if (is_array($diagnostics)) : ?>
            <div class="row mt-2">
                <div class="col-12">
                    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Why the message would or would not be masked');?></h6>

                    <?php if ($diagnostics['masked'] === true) : ?>
                        <div class="alert alert-success p-2 mb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Message WOULD be masked');?></div>
                    <?php else : ?>
                        <div class="alert alert-danger p-2 mb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Message would NOT be masked');?></div>
                    <?php endif; ?>

                    <table class="table table-sm table-condensed">
                        <tbody>
                        <?php foreach ($diagnostics['steps'] as $step) : ?>
                            <tr>
                                <td class="text-nowrap">
                                    <?php if ($step['ok'] === true) : ?>
                                        <span class="badge bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Pass');?></span>
                                    <?php elseif ($step['ok'] === false) : ?>
                                        <span class="badge bg-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/message_protection','Fail');?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($step['label'])?></td>
                                <td><?php echo htmlspecialchars($step['detail'])?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
