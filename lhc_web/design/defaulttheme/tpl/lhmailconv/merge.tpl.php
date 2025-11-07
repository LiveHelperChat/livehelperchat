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
                    <?php 
                    $limitation = erLhcoreClassChat::getDepartmentLimitation('lhc_mailconv_conversation', ['check_list_permissions' => true, 'check_list_scope' => 'mails']);
                    $filterParams = ['sort' => 'id DESC', 'limit' => 20, 'filter' => ['from_address' => $mail->from_address]];
                    if ($limitation !== false) {
                        if ($limitation !== true) {
                            $filterParams['filter']['customfilter'][] = $limitation;
                        }
                    } else {
                        $filterParams['filter']['customfilter'][] = '1 = -1';
                    }
                    // Check for permission to merge_cross_departments
                    if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv', 'merge_cross_departments')) {
                        $filterParams['filter']['dep_id'] = $mail->dep_id;
                    }
                    ?>
                    <?php $communications = erLhcoreClassModelMailconvConversation::getList($filterParams); ?>
                    <div class="col-6">
                        <i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Choose mail to merge')?></i>
                        <hr class="mt-1"/>
                        <?php foreach ($communications as $communication) : if ($mail->id != $communication->id) : ?>
                            <div class="form-group my-0"><label class="fs13 mb-1 text-truncate" style="max-width: 500px;"><input <?php if (in_array($communication->id,$input_data['source_mail'])) : ?>checked="checked"<?php endif;?> type="checkbox" name="source_mail[]" value="<?php echo $communication->id?>" data-department="<?php echo htmlspecialchars($communication->dep_id); ?>" /> <?php echo $communication->id?> | <span class="material-icons">home</span><?php echo htmlspecialchars((string)$communication->department); ?> | <?php echo htmlspecialchars($communication->subject);?></label></div>
                        <?php endif; endforeach; ?>
                    </div>
                    <div class="col-6">
                        <i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Choose destination mail')?></i>
                        <hr class="mt-1"/>
                        <?php foreach ($communications as $communication) : ?>
                            <div class="form-group my-0">
                                <label style="max-width: 500px;" class="fs13 mb-1 text-truncate <?php if ($mail->id == $communication->id) : ?>fw-bold<?php endif;?>" ><input <?php if ($input_data['merge_destination'] == $communication->id) : ?>checked="checked"<?php endif;?> type="radio" name="merge_destination" value="<?php echo $communication->id?>" data-department="<?php echo htmlspecialchars($communication->dep_id); ?>" /> <?php echo $communication->id?> | <span class="material-icons">home</span><?php echo htmlspecialchars((string)$communication->department); ?> | <?php echo htmlspecialchars($communication->subject);?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="department-warning" class="alert alert-info mt-3" style="display: none;">
                    <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Info')?>:</strong> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Selected conversations belong to different departments.')?>
                </div>
            </div>

            <script>
                function checkDepartmentConsistency() {
                    var selectedDepartments = new Set();
                    
                    // Get selected source mails
                    $('input[name="source_mail[]"]:checked').each(function() {
                        selectedDepartments.add($(this).data('department'));
                    });
                    
                    // Get selected destination mail
                    var destinationDept = $('input[name="merge_destination"]:checked').data('department');
                    if (destinationDept) {
                        selectedDepartments.add(destinationDept);
                    }
                    
                    // Show warning if more than one department
                    if (selectedDepartments.size > 1) {
                        $('#department-warning').show();
                    } else {
                        $('#department-warning').hide();
                    }
                }
                
                // Check on page load
                checkDepartmentConsistency();
                
                // Check when selections change
                $('input[name="source_mail[]"], input[name="merge_destination"]').on('change', function() {
                    checkDepartmentConsistency();
                });
            </script>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Merge selected');?></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Close');?></button>
            </div>
        </form>
    </div>
</div>

