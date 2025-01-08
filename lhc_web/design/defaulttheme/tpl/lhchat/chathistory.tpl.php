
            <?php if ($chatOriginal->cls_time == 0) : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Present chat was not closed yet. This information will be inaccurate at the moment.')?></p>
            <?php else : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','It shows only chats if they were closed. We show maximum of 10 chats in total.')?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Closed at')?> - <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chatOriginal->cls_time);?></p>
            <?php endif; ?>

            <ul class="nav nav-pills mb-3" role="tablist">
                <li role="presentation" class="nav-item"><a href="#chats-history" class="nav-link active" aria-controls="user-status" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Concurrent chats');?></a></li>
                <li role="presentation" class="nav-item"><a href="#live-chats" class="nav-link" aria-controls="online-hours" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Active operator chats');?></a></li>
            </ul>
            <div class="tab-content" ng-non-bindable>
                <div role="tabpanel" class="tab-pane active" id="chats-history" style="max-height: 550px;overflow-y: auto">

                    <?php $hideStatusText = true;?>
                    <table class="table table-hover table-small">
                        <thead>
                        <tr>
                            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','ID')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Chat duration')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Started')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Ended')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Previous chat assigned')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Current chat assigned')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Finished assign')?></th>
                            <th>
                                <i class="material-icons chat-pending" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Pending Chats')?>">chat</i>
                            </th>
                            <th>
                                <i class="material-icons chat-active" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active Chats')?>">chat</i>
                            </th>
                            <th>
                                <i class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Inactive Chats')?>">chat</i>
                            </th>
                        </tr>
                        </thead>
                        <?php foreach ($previousChats as $chat) :
                            $assignHistoryData = [];
                            $assignHistory = erLhcoreClassModelmsg::findOne(['customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.content.assign_action.user_id\') = '.$chat->user_id], 'filter' => ['user_id' => -1, 'chat_id' => $chat->id]]);
                            if (is_object($assignHistory)){
                                $assignHistoryData = json_decode($assignHistory->meta_msg, true);
                            }
                            ?>
                            <tr>
                                <td nowrap="nowrap"><a class="material-icons" onclick="lhc.previewChat(<?php echo $chat->id?>)">info_outline</a><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_column.tpl.php'));?><?php echo $chat->id?></td>
                                <td>
                                    <?php if ($chat->chat_duration > 0) : ?>
                                        <?php echo erLhcoreClassChat::formatSeconds($chat->chat_duration)?>
                                    <?php endif; ?>
                                </td>
                                <td nowrap=""><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->time :  $chat->time/1000);?></td>
                                <td nowrap=""><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->cls_time :  $chat->cls_time/1000);?></td>
                                <td nowrap="">
                                    <?php if (isset($assignHistoryData['content']['assign_action']['last_accepted'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['last_accepted'] > 0 ? date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['last_accepted']) : 'n/a';?>
                                    <?php endif; ?>
                                </td>
                                <td nowrap="">
                                    <?php if (isset($assignHistoryData['content']['assign_action']['sla'])) : ?>
                                        <?php if ($assignHistoryData['content']['assign_action']['sla'] && $assignHistoryData['content']['assign_action']['sla'] == 1) : ?>
                                            <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned was updated successfully')?>">done</span>
                                        <?php else : ?>
                                            <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned update failed')?>">clear</span>
                                        <?php endif; ?>
                                    <?php endif;?>
                                    <?php if (is_object($assignHistory)) : ?>
                                        <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistory->time);?>
                                    <?php endif; ?>
                                </td>
                                <td nowrap="">
                                    <?php if (isset($assignHistoryData['content']['assign_action']['sac'])) : ?>
                                        <?php if ($assignHistoryData['content']['assign_action']['sac'] && $assignHistoryData['content']['assign_action']['sac'] == 1) : ?>
                                            <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats were updated successfully')?>">done</span>
                                        <?php else : ?>
                                            <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats update failed')?>">clear</span>
                                        <?php endif; ?>
                                    <?php endif;?>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['assign_finished'])) : ?>
                                        <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['assign_finished']);?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['pending_chats'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['pending_chats'];?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['active_chats'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['active_chats'];?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['inactive_chats'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['inactive_chats'];?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php
                        $assignHistoryData = [];
                        $assignHistory = erLhcoreClassModelmsg::findOne(['customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.content.assign_action.user_id\') = '.$chatOriginal->user_id], 'filter' => ['user_id' => -1, 'chat_id' => $chatOriginal->id]]);
                        if (is_object($assignHistory)){
                            $assignHistoryData = json_decode($assignHistory->meta_msg, true);
                        }
                        ?>
                        <tr class="bg-light">
                            <td class="border-top border-bottom"><?php echo $chatOriginal->id?></td>
                            <td class="border-top border-bottom"><?php echo $chatOriginal->chat_duration_front?></td>
                            <td class="border-top border-bottom"><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chatOriginal->time);?></td>
                            <td class="border-top border-bottom">
                                <?php if ($chatOriginal->cls_time > 0) : ?>
                                    <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chatOriginal->cls_time);?>
                                <?php else : ?>
                                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Chat is still running')?>
                                <?php endif; ?>
                            </td>
                            <td nowrap="">
                                <?php if (isset($assignHistoryData['content']['assign_action']['last_accepted'])) : ?>
                                    <?php echo $assignHistoryData['content']['assign_action']['last_accepted'] > 0 ? date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['last_accepted']) : 'n/a';?>
                                <?php endif; ?>
                            </td>
                            <td nowrap="">
                                <?php if (isset($assignHistoryData['content']['assign_action']['sla'])) : ?>
                                    <?php if ($assignHistoryData['content']['assign_action']['sla'] && $assignHistoryData['content']['assign_action']['sla'] == 1) : ?>
                                        <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned was updated successfully')?>">done</span>
                                    <?php else : ?>
                                        <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned update failed')?>">clear</span>
                                    <?php endif; ?>
                                <?php endif;?>
                                <?php if (is_object($assignHistory)) : ?>
                                    <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistory->time);?>
                                <?php endif; ?>
                            </td>
                            <td nowrap="">
                                <?php if (isset($assignHistoryData['content']['assign_action']['sac'])) : ?>
                                    <?php if ($assignHistoryData['content']['assign_action']['sac'] && $assignHistoryData['content']['assign_action']['sac'] == 1) : ?>
                                        <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats were updated successfully')?>">done</span>
                                    <?php else : ?>
                                        <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats update failed')?>">clear</span>
                                    <?php endif; ?>
                                <?php endif;?>
                                <?php if (isset($assignHistoryData['content']['assign_action']['assign_finished'])) : ?>
                                    <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['assign_finished']);?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($assignHistoryData['content']['assign_action']['pending_chats'])) : ?>
                                    <?php echo $assignHistoryData['content']['assign_action']['pending_chats'];?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($assignHistoryData['content']['assign_action']['active_chats'])) : ?>
                                    <?php echo $assignHistoryData['content']['assign_action']['active_chats'];?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($assignHistoryData['content']['assign_action']['inactive_chats'])) : ?>
                                    <?php echo $assignHistoryData['content']['assign_action']['inactive_chats'];?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php foreach ($nextChats as $chat) :
                            $assignHistoryData = [];
                            $assignHistory = erLhcoreClassModelmsg::findOne(['customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.content.assign_action.user_id\') = '.$chat->user_id], 'filter' => ['user_id' => -1, 'chat_id' => $chat->id]]);
                            if (is_object($assignHistory)){
                                $assignHistoryData = json_decode($assignHistory->meta_msg, true);
                            }
                            ?>
                            <tr>
                                <td><a class="material-icons" onclick="lhc.previewChat(<?php echo $chat->id?>)">info_outline</a><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_column.tpl.php'));?><?php echo $chat->id?></td>
                                <td>
                                    <?php if ($chat->chat_duration > 0) : ?>
                                        <?php echo erLhcoreClassChat::formatSeconds($chat->chat_duration)?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->time :  $chat->time/1000);?></td>
                                <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->cls_time :  $chat->cls_time/1000);?></td>
                                <td nowrap="">
                                    <?php if (isset($assignHistoryData['content']['assign_action']['last_accepted'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['last_accepted'] > 0 ? date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['last_accepted']) : 'n/a';?>
                                    <?php endif; ?>
                                </td>
                                <td nowrap="">
                                    <?php if (isset($assignHistoryData['content']['assign_action']['sla'])) : ?>
                                        <?php if ($assignHistoryData['content']['assign_action']['sla'] && $assignHistoryData['content']['assign_action']['sla'] == 1) : ?>
                                            <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned was updated successfully')?>">done</span>
                                        <?php else : ?>
                                            <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned update failed')?>">clear</span>
                                        <?php endif; ?>
                                    <?php endif;?>
                                    <?php if (is_object($assignHistory)) : ?>
                                        <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistory->time);?>
                                    <?php endif; ?>
                                </td>
                                <td nowrap="">
                                    <?php if (isset($assignHistoryData['content']['assign_action']['sac'])) : ?>
                                        <?php if ($assignHistoryData['content']['assign_action']['sac'] && $assignHistoryData['content']['assign_action']['sac'] == 1) : ?>
                                            <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats were updated successfully')?>">done</span>
                                        <?php else : ?>
                                            <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats update failed')?>">clear</span>
                                        <?php endif; ?>
                                    <?php endif;?>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['assign_finished'])) : ?>
                                        <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['assign_finished']);?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['pending_chats'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['pending_chats'];?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['active_chats'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['active_chats'];?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($assignHistoryData['content']['assign_action']['inactive_chats'])) : ?>
                                        <?php echo $assignHistoryData['content']['assign_action']['inactive_chats'];?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="live-chats" style="max-height: 550px;overflow-y: auto" ng-non-bindable>
                    <table class="table table-hover table-sm">
                        <thead>
                        <tr>
                            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','ID')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Chat duration')?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Started')?></th>
                        </tr>
                        </thead>
                        <?php foreach ($activeChats as $chat) : ?>
                            <tr>
                                <td nowrap="nowrap">
                                    <a class="material-icons" onclick="lhc.previewChat(<?php echo $chat->id?>)">info_outline</a>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_column.tpl.php'));?><?php echo $chat->id?>
                                </td>
                                <td><?php echo $chat->chat_duration_front?></td>
                                <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>



