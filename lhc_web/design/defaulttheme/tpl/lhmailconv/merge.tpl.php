<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/merge')?>/<?php echo $mail->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
            <div class="modal-body">
                <?php if (isset($errors)) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
                <?php endif; ?>

                <?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mails were merged!'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
                    <script>
                        ee.emitEvent('mailChatModified', [<?php echo $mail->id?>]);
                    </script>
                <?php endif; ?>

                <div class="row">
                    <?php $communications = erLhcoreClassModelMailconvConversation::getList(['sort' => 'id DESC', 'limit' => 20, 'filter' => ['from_address' => $mail->from_address]]); ?>
                    <div class="col-6">
                        <i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Choose mail to merge')?></i>
                        <hr class="mt-1"/>
                        <?php foreach ($communications as $communication) : if ($mail->id != $communication->id) : ?>
                            <div class="form-group my-0"><label class="fs13 mb-1 text-truncate" style="max-width: 500px;"><input <?php if (in_array($communication->id,$input_data['source_mail'])) : ?>checked="checked"<?php endif;?> type="checkbox" name="source_mail[]" value="<?php echo $communication->id?>" /> <?php echo $communication->id?> | <?php echo htmlspecialchars($communication->subject);?></label></div>
                        <?php endif; endforeach; ?>
                    </div>
                    <div class="col-6">
                        <i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Choose destination mail')?></i>
                        <hr class="mt-1"/>
                        <?php foreach ($communications as $communication) : ?>
                            <div class="form-group my-0">
                                <label style="max-width: 500px;" class="fs13 mb-1 text-truncate <?php if ($mail->id == $communication->id) : ?>fw-bold<?php endif;?>" ><input <?php if ($input_data['merge_destination'] == $communication->id) : ?>checked="checked"<?php endif;?> type="radio" name="merge_destination" value="<?php echo $communication->id?>" /> <?php echo $communication->id?> | <?php echo htmlspecialchars($communication->subject);?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Merge selected');?></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Close');?></button>
            </div>
        </form>
    </div>
</div>

