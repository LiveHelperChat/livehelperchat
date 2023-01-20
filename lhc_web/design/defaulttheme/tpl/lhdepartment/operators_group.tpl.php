<?php if ($group_op === null && $only_online === null && $only_logged === null && $only_offline === null) : ?>
<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2 ';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group operators');
$modalBodyClass = 'p-1';
$modalSize = 'xl';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div class="modal-body" ng-non-bindable>
        <div class="p-2">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','We show only to group assigned operators. We do not show directly to department assigned operators.');?></p>
            <label class="me-2"><input type="checkbox" id="id_group_user" name="group_user"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Group by operator');?></label>
            <label class="me-2"><input type="checkbox" id="id_only_online" name="only_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Only online');?></label>
            <label class="me-2"><input type="checkbox" id="id_only_offline" name="only_offline"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Only offline');?></label>
            <label><input type="checkbox" id="id_only_logged" name="only_logged"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Only logged');?></label>
            <?php endif;?>
            <table id="table-operators" class="table table-sm table-hover">
                <thead>
                <tr>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','User ID');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Last activity ago');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Read only');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Assignment type');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Exclude from auto assign workflow');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Max chats');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Active chats');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Inactive chats');?></th>
                </tr>
                </thead>
                <?php
                $paramsGroup = ['sort' => 'user_id ASC, type ASC','filter' => ['dep_group_id' => $department_group->id], 'limit' => false];
                if ($group_op === true) {
                    $paramsGroup['group'] = 'user_id';
                }

                if ($only_online === true) {
                    $paramsGroup['customfilter'][] = '(`hide_online` = 0 AND (`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ' OR `always_on` = 1))';
                }

                if ($only_logged === true) {
                    $paramsGroup['customfilter'][] = '(`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ')';
                }

                if ($only_offline === true) {
                    $paramsGroup['customfilter'][] = '(`hide_online` = 1 AND (`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ' OR `always_on` = 1))';
                }

                foreach (erLhcoreClassModelUserDep::getList($paramsGroup) as $member) : ?>
                    <tr>
                        <td id="<?php echo $member->user_id?>">
                            <a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $member->user_id?>" target="_blank"><span class="material-icons">open_in_new</span><?php echo htmlspecialchars($member->name_official)?></a>
                        </td>
                        <td>
                           <?php echo $group_op === true ? '-' : htmlspecialchars(erLhcoreClassModelDepartament::fetch($member->dep_id))?>
                        </td>
                        <td>
                            <span class="material-icons"><?php echo $member->hide_online == 0 ? 'flash_on' : 'flash_off';?></span>
                            <?php $agoActivity = time() - $member->last_activity; ?>
                            <?php echo $agoActivity > 0 ? erLhcoreClassChat::formatSeconds($agoActivity) : '0 s.'; ?>
                        </td>
                        <td>
                            <?php if ($member->ro == 1) : ?>
                                <span class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?></span>
                            <?php else : ?>
                                <span class="text-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($member->type == 0) : ?>
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department');?>
                            <?php else : ?>
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group');?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(erLhcoreClassModelDepartamentGroup::fetch($member->dep_group_id));?> [<?php echo $member->dep_group_id?>]
                        </td>
                        <td>
                            <?php if ($member->exclude_autoasign == 1 || $member->exc_indv_autoasign == 1) : ?>
                                <span class="material-icons"><?php if ($member->exclude_autoasign == 1) : ?>home<?php else : ?>account_balance<?php endif;?></span><span class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?></span>
                            <?php else : ?>
                                <span class="text-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $member->max_chats?></td>
                        <td><?php echo $member->active_chats?></td>
                        <td><?php echo $member->inactive_chats?></td>

                    </tr>
                <?php endforeach; ?>
            </table>

            <?php if ($group_op === null && $only_online === null) : ?>
        </div>
    </div>
    <script>
        $('.abbr-list-lang').tooltip();
        $('#id_group_user,#id_only_online,#id_only_logged,#id_only_offline').change(function(){
            var groupItem = $('#id_group_user');
            var onlyOnline = $('#id_only_online');
            var onlyLogged = $('#id_only_logged');
            var onlyOffline = $('#id_only_offline');
            $.get(WWW_DIR_JAVASCRIPT + 'department/editgroup/<?php echo $department_group->id?>/(action)/operators?group=' + groupItem.is(':checked') + '&only_online=' + onlyOnline.is(':checked') + '&only_logged=' + onlyLogged.is(':checked') + '&only_offline=' + onlyOffline.is(':checked'), function(data){
                $('#table-operators').replaceWith(data);
            });
        });
    </script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
<?php endif; ?>
