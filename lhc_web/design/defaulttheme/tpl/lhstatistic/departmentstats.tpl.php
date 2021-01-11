<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Statistic') . ' - ' . (isset($department) ? htmlspecialchars($department) : htmlspecialchars($department_group));
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div class="modal-body">
    <div class="p-2">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li role="presentation" class="nav-item"><a href="#dep-status" class="nav-link active" aria-controls="dep-status" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Chats');?></a></li>
            <li role="presentation" class="nav-item"><a href="#user-status" class="nav-link" aria-controls="user-status" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operators');?></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="dep-status">
                <h6>Chats statistic</h6>
                <?php if (isset($department)) : $department_live = clone $department; erLhcoreClassChatStatsResque::updateDepartmentStats($department_live, false); ?>
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->active_chats_counter?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->active_chats_counter?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','active chats');?><br/><span class="badge badge-light">active_chats_counter</span>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->bot_chats_counter?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->bot_chats_counter?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','bot chats');?><br/><span class="badge badge-light">bot_chats_counter</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->pending_chats_counter?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->pending_chats_counter?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','pending chats');?><br/><span class="badge badge-light">pending_chats_counter</span>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->inactive_chats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->inactive_chats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','in-active chats');?><br/><span class="badge badge-light">inactive_chats_cnt</span>
                                    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Chat is active/pending and user has closed widget or has been redirected to survey');?></small>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Load statistic');?></h6>
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->max_load?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->max_load?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','soft limit');?><br/><span class="badge badge-light">max_load</span>
                                    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator is logged to back office during last 10 minutes and is online/offline');?></small>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->max_load_h?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->max_load_h?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','hard limit');?><br/><span class="badge badge-light">max_load_h</span>
                                    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator is logged to back office during last 10 minutes and is in online status');?></small>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->acop_chats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->acop_chats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','active operators chats');?><br/><span class="badge badge-light">acop_chats_cnt</span>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department->inop_chats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_live->inop_chats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','inactive operators chats');?><br/><span class="badge badge-light">inop_chats_cnt</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Summary statistic [Hard limit]');?></h6>
                            <ul class="list-unstyled">
                                <li>
                                    <span class="badge badge-light">max_load_h</span> - ( <span class="badge badge-light">acop_chats_cnt</span> - <span class="badge badge-light">inop_chats_cnt</span> )
                                </li>
                                <li>
                                    <?php echo (int)$department->max_load_h. ' - ('. (int)$department->acop_chats_cnt . ' - ' . (int)$department->inop_chats_cnt . ')'; ?> = <?php echo (int)$department->max_load_h  - ( (int)$department->acop_chats_cnt  -  (int)$department->inop_chats_cnt ); ?>
                                    <br/><?php echo (int)$department_live->max_load_h. ' - ('. (int)$department_live->acop_chats_cnt . ' - ' . (int)$department_live->inop_chats_cnt . ')'; ?> = <?php echo (int)$department_live->max_load_h  - ( (int)$department_live->acop_chats_cnt  -  (int)$department_live->inop_chats_cnt ); ?> <span class="material-icons">autorenew</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Summary statistic [Soft limit]');?></h6>
                            <ul class="list-unstyled">
                                <li>
                                    <span class="badge badge-light">max_load</span> - ( <span class="badge badge-light">active_chats_counter</span> - <span class="badge badge-light">inactive_chats_cnt</span> )
                                </li>
                                <li>
                                    <?php echo (int)$department->max_load. ' - ('. (int)$department->active_chats_counter . ' - ' . (int)$department->inactive_chats_cnt . ')'; ?> = <?php echo (int)$department->max_load  - ( (int)$department->active_chats_counter  -  (int)$department->inactive_chats_cnt ); ?>
                                    <br/><?php echo (int)$department_live->max_load. ' - ('. (int)$department_live->active_chats_counter . ' - ' . (int)$department_live->inactive_chats_cnt . ')'; ?> = <?php echo (int)$department_live->max_load  - ( (int)$department_live->active_chats_counter  -  (int)$department_live->inactive_chats_cnt ); ?> <span class="material-icons">autorenew</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                <?php else : ?>

                    <?php $department_group_live = clone $department_group; erLhcoreClassChatStatsResque::updateDepartmentGroupStats($department_group_live, false); ?>
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->achats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->achats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','active chats');?><br/><span class="badge badge-light">achats_cnt</span>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->bchats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->bchats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','bot chats');?><br/><span class="badge badge-light">bchats_cnt</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->pchats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->pchats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','pending chats');?><br/><span class="badge badge-light">pchats_cnt</span>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->inachats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->inachats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','in-active chats');?><br/><span class="badge badge-light">inachats_cnt</span>
                                    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Chat is active/pending and user has closed widget or has been redirected to survey');?></small>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Load statistic');?></h6>
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->max_load?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->max_load?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','soft limit');?><br/><span class="badge badge-light">max_load</span>
                                    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator is logged to back office during last 10 minutes and is online/offline');?></small>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->max_load_h?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->max_load_h?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','hard limit');?><br/><span class="badge badge-light">max_load_h</span>
                                    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator is logged to back office during last 10 minutes and is in online status');?></small>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->acopchats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->acopchats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','active operators chats');?><br/><span class="badge badge-light">acopchats_cnt</span>
                                </li>
                                <li>
                                    <span class="material-icons">history</span><strong><?php echo $department_group->inopchats_cnt?></strong><small title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Live data');?>" class="pl-1">[<?php echo $department_group_live->inopchats_cnt?>]</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','inactive operators chats');?><br/><span class="badge badge-light">inopchats_cnt</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Summary statistic [Hard limit]');?></h6>
                            <ul class="list-unstyled">
                                <li>
                                    <span class="badge badge-light">max_load_h</span> - ( <span class="badge badge-light">acopchats_cnt</span> - <span class="badge badge-light">inopchats_cnt</span> )
                                </li>
                                <li>
                                    <?php echo (int)$department_group->max_load_h. ' - ('. (int)$department_group->acopchats_cnt . ' - ' . (int)$department_group->inopchats_cnt . ')'; ?> = <?php echo (int)$department_group->max_load_h  - ( (int)$department_group->acopchats_cnt  -  (int)$department_group->inopchats_cnt ); ?>
                                    <br/><?php echo (int)$department_group_live->max_load_h. ' - ('. (int)$department_group_live->acopchats_cnt . ' - ' . (int)$department_group_live->inopchats_cnt . ')'; ?> = <?php echo (int)$department_group_live->max_load_h  - ( (int)$department_group_live->acopchats_cnt  -  (int)$department_group_live->inopchats_cnt ); ?> <span class="material-icons">autorenew</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Summary statistic [Soft limit]');?></h6>
                            <ul class="list-unstyled">
                                <li>
                                    <span class="badge badge-light">max_load</span> - ( <span class="badge badge-light">achats_cnt</span> - <span class="badge badge-light">inachats_cnt</span> )
                                </li>
                                <li>
                                    <?php echo (int)$department_group->max_load. ' - ('. (int)$department_group->achats_cnt . ' - ' . (int)$department_group->inachats_cnt . ')'; ?> = <?php echo (int)$department_group->max_load  - ( (int)$department_group->achats_cnt  -  (int)$department_group->inachats_cnt ); ?>
                                    <br/><?php echo (int)$department_group_live->max_load. ' - ('. (int)$department_group_live->achats_cnt . ' - ' . (int)$department_group_live->inachats_cnt . ')'; ?> = <?php echo (int)$department_group_live->max_load  - ( (int)$department_group_live->achats_cnt  -  (int)$department_group_live->inachats_cnt ); ?> <span class="material-icons">autorenew</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
            <div role="tabpanel" class="tab-pane mx550" id="user-status">

                    <?php
                    if (isset($department)) {
                        $operatorsStatus = erLhcoreClassChatStatsResque::getDepartmentOperatorsStatistic($department);
                        $operatorsStatusHard = erLhcoreClassChatStatsResque::getDepartmentOperatorsStatistic($department, false);
                    } else {
                        $operatorsStatus = erLhcoreClassChatStatsResque::getDepartmentGroupOperatorsStatistic($department_group);
                        $operatorsStatusHard = erLhcoreClassChatStatsResque::getDepartmentGroupOperatorsStatistic($department_group, false);
                    }
                    ?>

                    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Soft limit. Operator is logged to back office during last 10 minutes and is online/offline.');?></h6>
                    <table class="table table-sm table-striped w-100">
                        <thead>
                            <tr>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','User ID');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Max chats');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Active chats');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Inactive chats');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Online');?></th>
                            </tr>
                        </thead>
                        <?php $totalStats = array('max_chats' => 0, 'active_chats' => 0, 'inactive_chats' => 0); foreach ($operatorsStatus as $operator) : ?>
                        <tr>
                            <td><a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $operator['user_id']?>"><?php echo $operator['user_id']?></a></td>
                            <td><?php echo $operator['max_chats'];$totalStats['max_chats'] += $operator['max_chats']?></td>
                            <td><?php echo $operator['active_chats'];$totalStats['active_chats'] += $operator['active_chats']?></td>
                            <td><?php echo $operator['inactive_chats'];$totalStats['inactive_chats'] += $operator['inactive_chats']?></td>
                            <td>
                                <span class="material-icons"><?php echo $operator['hide_online'] == 0 ? 'flash_on' : 'flash_off';?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Total');?></td>
                            <td>
                                <?php echo $totalStats['max_chats']?> <span class="badge badge-light">max_load</span>
                            </td>
                            <td>
                                <?php echo $totalStats['active_chats']?> <span class="badge badge-light"><?php if (isset($department)) : ?>acop_chats_cnt<?php else : ?>acopchats_cnt<?php endif; ?></span>
                            </td>
                            <td colspan="2">
                                <?php echo $totalStats['inactive_chats']?> <span class="badge badge-light"><?php if (isset($department)) : ?>inop_chats_cnt<?php else : ?>inopchats_cnt<?php endif; ?></span>
                            </td>
                        </tr>
                    </table>

                    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Hard limit. Operator is logged to back office during last 10 minutes and is in online status');?></h6>
                    <table class="table table-sm table-striped w-100">
                        <thead>
                            <tr>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','User ID');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Max chats');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Active chats');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Inactive chats');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Online');?></th>
                            </tr>
                        </thead>
                        <?php $totalStats = array('max_chats' => 0, 'active_chats' => 0, 'inactive_chats' => 0); foreach ($operatorsStatusHard as $operator) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $operator['user_id']?>"><?php echo $operator['user_id']?></a>
                            </td>
                            <td><?php echo $operator['max_chats'];$totalStats['max_chats'] += $operator['max_chats']?></td>
                            <td><?php echo $operator['active_chats'];$totalStats['active_chats'] += $operator['active_chats']?></td>
                            <td><?php echo $operator['inactive_chats'];$totalStats['inactive_chats'] += $operator['inactive_chats']?></td>
                            <td><span class="material-icons"><?php echo $operator['hide_online'] == 0 ? 'flash_on' : 'flash_off';?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Total');?></td>
                            <td>
                                <?php echo $totalStats['max_chats']?> <span class="badge badge-light">max_load_h</span>
                            </td>
                            <td>
                                <?php echo $totalStats['active_chats']?> <span class="badge badge-light"><?php if (isset($department)) : ?>acop_chats_cnt<?php else : ?>acopchats_cnt<?php endif; ?></span>
                            </td>
                            <td colspan="2">
                                <?php echo $totalStats['inactive_chats']?> <span class="badge badge-light"><?php if (isset($department)) : ?>inop_chats_cnt<?php else : ?>inopchats_cnt<?php endif; ?></span>
                            </td>
                        </tr>
                    </table>

            </div>
        </div>
    </div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>