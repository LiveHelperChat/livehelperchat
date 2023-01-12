<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2 ';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Statistic');
$modalBodyClass = 'p-1';
$modalSize = 'xl';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
    <div class="modal-body" ng-non-bindable>
        <div class="p-2">

            <div class="pb-2">
                <a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>" class="pe-2"><span class="material-icons">edit</span> <?php echo htmlspecialchars($user->name_official)?> [<?php echo $user->id?>]</a>
                <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_ids)/<?php echo $user->id?>/(chat_status_ids)/0/1" class="pe-2"><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator active/pending chats');?>" class="material-icons">chat</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator active/pending chats');?></a>
                <a href="<?php echo erLhcoreClassDesign::baseurl('statistic/onlinehours')?>/(user_id)/<?php echo $user->id?>" class="pe-2"><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Online hours');?>" class="material-icons">schedule</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator online hours');?></a>
                <a href="<?php echo erLhcoreClassDesign::baseurl('audit/loginhistory')?>/(user_id)/<?php echo $user->id?>" class="pe-2"><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Login history');?>" class="material-icons">schedule</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Login history');?></a>
                <div>
                    <?php $itemLanguages = erLhcoreClassModelSpeechUserLanguage::getList(['filter' => ['user_id' => $user->id]]);foreach ($itemLanguages as $lang) : ?><span class="badge bg-success me-1"><?php echo htmlspecialchars($lang->language)?></span><?php endforeach; ?>
                </div>
            </div>
            <ul class="nav nav-pills mb-3" role="tablist">
                <li role="presentation" class="nav-item"><a href="#user-status" class="nav-link active" aria-controls="user-status" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','User stats');?></a></li>
                <li role="presentation" class="nav-item"><a href="#online-hours" class="nav-link" aria-controls="online-hours" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Online hours');?></a></li>
                <li role="presentation" class="nav-item"><a href="#login-history" class="nav-link" aria-controls="login-history" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Login history');?></a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="online-hours" style="max-height: 550px;overflow-y: auto">
                    <table class="table table-sm table-hover" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
                        <thead>
                        <tr>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Start activity');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Duration');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Chats served');?></th>
                        </tr>
                        </thead>
                        <?php $parentItem = null;
                        $onlineSessions = erLhcoreClassModelUserOnlineSession::getList(array('filter' => ['user_id' => $user->id],'offset' => 0, 'limit' => 20,'sort' => 'id DESC'));
                        erLhcoreClassModelUserOnlineSession::setChatsBySessions($onlineSessions,['filterin' => ['user_id' => [$user->id]]]);
                        foreach ($onlineSessions as $item) : ?>
                            <?php if (is_object($parentItem)) : ?>
                                <tr>
                                    <td colspan="2">
                                    </td>
                                    <td colspan="1">
                                        <div class="text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Was offline for');?>"><b><?php echo erLhcoreClassChat::formatSeconds($parentItem->time - $item->lactivity)?></b></div>
                                    </td>
                                    <td>
                                        <?php if ( $item->chatsOffline > 0) : ?>
                                            <a class="text-danger" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_ids)/<?php echo $item->user_id?>/(timeto_seconds)/<?php echo date('s',$parentItem->time)?>/(timeto_minutes)/<?php echo date('i',$parentItem->time)?>/(timeto_hours)/<?php echo date('H',$parentItem->time)?>/(timeto)/<?php echo date('Y-m-d',$parentItem->time)?>/(timefrom)/<?php echo date('Y-m-d',$item->lactivity)?>/(timefrom_hours)/<?php echo date('H',$item->lactivity)?>/(timefrom_minutes)/<?php echo date('i',$item->lactivity)?>/(timefrom_seconds)/<?php echo date('s',$item->lactivity)?>" target="_blank"><span class="material-icons">open_in_new</span> <?php echo $item->chatsOffline?></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->time_front)?></td>
                                <td><?php echo htmlspecialchars($item->lactivity_front)?></td>
                                <td><?php echo $item->duration_front?></td>
                                <td>
                                <?php if ( $item->chatsOnline > 0) : ?>
                                    <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_ids)/<?php echo $item->user_id?>/(timeto_seconds)/<?php echo date('s',$item->lactivity)?>/(timeto_minutes)/<?php echo date('i',$item->lactivity)?>/(timeto_hours)/<?php echo date('H',$item->lactivity)?>/(timeto)/<?php echo date('Y-m-d',$item->lactivity)?>/(timefrom)/<?php echo date('Y-m-d',$item->time)?>/(timefrom_hours)/<?php echo date('H',$item->time)?>/(timefrom_minutes)/<?php echo date('i',$item->time)?>/(timefrom_seconds)/<?php echo date('s',$item->time)?>" target="_blank"><span class="material-icons">open_in_new</span> <?php echo $item->chatsOnline?></a>
                                <?php endif; ?>
                                </td>
                            </tr>
                        <?php $parentItem = $item; endforeach; ?>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane active" id="user-status" style="max-height: 550px;overflow-y: auto">
                    <?php $operatorsStatus = erLhcoreClassChatStatsResque::getUserStats($user); ?>
                    <table class="table table-sm table-striped w-100">
                        <thead>
                        <tr>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Last activity ago');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Last chat assigned ago');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Max chats');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Active chats');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Inactive chats');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Assignment type');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Read only');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Exclude from auto assign workflow');?></th>
                        </tr>
                        </thead>
                        <?php $totalStats = array('max_chats' => 0, 'active_chats' => 0, 'inactive_chats' => 0); foreach ($operatorsStatus as $operator) : ?>
                            <tr>
                                <td>
                                    <span class="material-icons"><?php echo $operator['hide_online'] == 0 ? 'flash_on' : 'flash_off';?></span>
                                    <?php $agoActivity = time() - $operator['last_activity']; ?>
                                    <?php echo $agoActivity > 0 ? erLhcoreClassChat::formatSeconds($agoActivity) : '0 s.'?>
                                </td>
                                <td>
                                    <?php if ( $operator['last_accepted'] > 0) : ?>
                                        <?php $agoActivity = time() - $operator['last_accepted']; ?>
                                        <?php echo $agoActivity > 0 ? erLhcoreClassChat::formatSeconds($agoActivity) : '0 s.'?>
                                    <?php else : ?>
                                        n/a
                                    <?php endif; ?>
                                </td>

                                <td><?php echo $operator['max_chats'];$totalStats['max_chats'] += $operator['max_chats']?></td>
                                <td><?php echo $operator['active_chats'];$totalStats['active_chats'] += $operator['active_chats']?></td>
                                <td><?php echo $operator['inactive_chats'];$totalStats['inactive_chats'] += $operator['inactive_chats']?></td>
                                <td nowrap="nowrap">
                                    <?php if ($operator['dep_id'] == 0) : ?>
                                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','All');?> [0]
                                    <?php else : ?>
                                    <?php echo htmlspecialchars(erLhcoreClassModelDepartament::fetch($operator['dep_id']))?> [<?php echo $operator['dep_id']?>]
                                    <?php endif; ?>
                                </td>
                                <td nowrap="nowrap">
                                    <?php echo htmlspecialchars(erLhcoreClassModelDepartamentGroup::fetch($operator['dep_group_id']));?> [<?php echo $operator['dep_group_id']?>]
                                </td>
                                <td nowrap="nowrap">
                                    <?php if ($operator['type'] == 0) : ?>
                                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department');?>
                                    <?php else : ?>
                                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group');?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($operator['ro'] == 1) : ?>
                                        <span class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?></span>
                                    <?php else : ?>
                                        <span class="text-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($operator['exclude_autoasign'] == 1 || $operator['exc_indv_autoasign'] == 1) : ?>
                                        <span class="text-danger">
                                            <span class="material-icons"><?php if ($operator['exclude_autoasign'] == 1) : ?>home<?php else : ?>account_balance<?php endif;?></span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?>
                                        </span>
                                    <?php else : ?>
                                        <span class="text-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="login-history" style="max-height: 550px;overflow-y: auto">

                    <table class="table table-sm" ng-non-bindable cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','ID');?></th>
                        <th width="1%" nowrap=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','User ID');?></th>
                        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Message');?></th>
                        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','IP');?></th>
                        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Date');?></th>
                    </tr>
                    </thead>
                    <?php foreach (erLhcoreClassModelUserLogin::getList(array('filter' => ['user_id' => $user->id],'offset' => 0, 'limit' => 20,'sort' => 'id DESC')) as $item) : ?>
                        <tr>
                            <td>
                                <?php echo $item->id?>
                            </td>
                            <td>
                                <?php echo $item->user_id?>
                            </td>
                            <td title="<?php echo $item->type?>">
                                <?php if ($item->status == erLhcoreClassModelUserLogin::STATUS_COMPLETED) : ?>
                                    <span class="material-icons text-success">done</span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item->msg)?>
                            </td>
                            <td>
                                <?php echo $item->ip?>
                            </td>
                            <td nowrap>
                                <?php echo $item->ctime_front?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </table>

                </div>
            </div>
        </div>
    </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>