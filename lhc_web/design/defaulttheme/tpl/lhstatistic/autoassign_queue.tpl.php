<?php
/**
 * Read only replay of the department auto assignment queue.
 *
 * Rendered in two places:
 *  - included by `lhstatistic/departmentstats.tpl.php` inside the `Next auto assignment` tab
 *  - rendered standalone by `statistic/departmentstats/(action)/autoassign` so the tab can refresh
 *    only this part every 2 seconds without reloading the whole modal.
 *
 * Expects `$department` (erLhcoreClassModelDepartament) to be set.
 */
$autoAssignQueue = \LiveHelperChat\Mcp\AutoAssign::queueOverview($department, 5);
?>
<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Next auto assignment');?></h6>
<p class="small text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','This is a read only replay of the auto assignment workflow. A chat is assigned by the `cron/workflow` cron job or by the next chat status check, so the operator and the order below can change in the meantime. Extension events (`chat.workflow.autoassign`, `chat.workflow.autoassign_permit`) are not visible here.');?></p>

<?php if ($autoAssignQueue['enabled'] !== true) : ?>
    <div class="alert alert-warning p-2 m-0 mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Auto assignment is disabled on this department (`active_balancing` = 0), pending chats wait until an operator accepts them manually.');?></div>
<?php endif; ?>

<?php if ($autoAssignQueue['simulated']) : ?>
    <div class="alert alert-info p-2 m-0 mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','There are no pending chats in this department. The operator queue below is simulated for a chat which just arrived with priority 0.');?></div>
<?php else : ?>
    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Pending chats in queue');?></h6>
    <table class="table table-sm table-striped w-100">
        <thead>
            <tr>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Chat');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Priority');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Waiting');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Auto assignment');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Variant');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Blocked by');?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($autoAssignQueue['chats'] as $queueChat) : ?>
            <tr>
                <td><a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/single')?>/<?php echo (int)$queueChat['chat']['id']?>">#<?php echo (int)$queueChat['chat']['id']?></a></td>
                <td><?php echo (int)$queueChat['chat']['priority']?></td>
                <td><?php echo erLhcoreClassChat::formatSeconds(max(0, time() - (int)$queueChat['chat']['ctime']))?></td>
                <td>
                    <?php if ($queueChat['gates']['allowed'] && $queueChat['pick']['picked_user_id'] > 0) : ?>
                        <span class="badge bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?></span>
                        <?php $queueChatUser = erLhcoreClassModelUser::fetch($queueChat['pick']['picked_user_id']); ?>
                        <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo (int)$queueChat['pick']['picked_user_id']?>"><?php echo htmlspecialchars($queueChatUser !== false ? $queueChatUser->name_official : '#' . (int)$queueChat['pick']['picked_user_id'])?></a>
                    <?php else : ?>
                        <span class="badge bg-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo $queueChat['pick']['variant'] === null ? '-' : htmlspecialchars((string)$queueChat['pick']['variant'])?></td>
                <td>
                    <?php foreach ($queueChat['gates']['blocked_by'] as $queueBlockedBy) : ?><span class="badge bg-secondary me-1"><?php echo htmlspecialchars($queueBlockedBy)?></span> <?php endforeach; ?>
                    <?php if (empty($queueChat['gates']['blocked_by']) && $queueChat['pick']['picked_user_id'] == 0) : ?><span class="badge bg-secondary">no_matching_operator</span><?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h6>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator queue');?>
    <?php if ((int)$autoAssignQueue['reference_chat']['id'] > 0) : ?>
        <span class="small text-muted">- #<?php echo (int)$autoAssignQueue['reference_chat']['id']?> (<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','priority');?> <?php echo (int)$autoAssignQueue['reference_chat']['priority']?>)</span>
    <?php endif; ?>
</h6>
<?php if (empty($autoAssignQueue['operators'])) : ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No operator is assigned to this department. Rows with `dep_id` 0 (all departments) are not used by the auto assignment queries.');?></p>
<?php else : ?>
    <div class="small text-muted mb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','At most');?> <?php echo (int)\LiveHelperChat\Mcp\AutoAssign::QUEUE_OPERATOR_LIMIT?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','next operators from the queue are listed, the rest is listed with the reason they are skipped.');?></div>
    <table class="table table-sm table-striped w-100">
        <thead>
            <tr>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Queue position');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Operator');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Online');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Last assigned ago');?></th>
                <th title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Active / pending / inactive chats');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Chats');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Max chats');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Assignment priority');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Verdict');?></th>
            </tr>
        </thead>
        <tbody>
        <?php $queuePosition = 0; foreach ($autoAssignQueue['operators'] as $queueOperator) : ?>
            <tr class="<?php if (!$queueOperator['pickable']) : ?>text-muted<?php endif;?>" data-autoassign-valid="<?php echo $queueOperator['pickable'] ? 1 : 0;?>">
                <td>
                    <?php if ($queueOperator['pickable']) : $queuePosition++; ?>
                        <span class="badge bg-success"><?php echo $queuePosition?></span>
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php $queueOperatorUser = erLhcoreClassModelUser::fetch($queueOperator['user_id']); ?>
                    <a <?php if (!$queueOperator['pickable']) : ?>class="text-muted"<?php endif;?> title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Edit operator');?>" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo (int)$queueOperator['user_id']?>"><span class="material-icons">account_box</span><?php echo htmlspecialchars($queueOperatorUser !== false ? $queueOperatorUser->name_official : '#' . (int)$queueOperator['user_id'])?></a>
                    <span class="badge bg-secondary">user_dep_id <?php echo (int)$queueOperator['user_dep_id']?></span>
                    <?php if ((int)$queueOperator['operator']['ro'] === 1) : ?><span class="badge bg-warning text-dark">ro</span><?php endif; ?>
                    <?php if ((int)$queueOperator['operator']['only_priority'] === 1) : ?><span class="badge bg-info text-dark">only_priority</span><?php endif; ?>
                    <?php if ((int)$queueOperator['operator']['always_on'] === 1) : ?><span class="badge bg-info text-dark">always_on</span><?php endif; ?>
                </td>
                <td>
                    <span class="material-icons <?php echo $queueOperator['online'] ? 'text-success' : 'text-danger';?>" title="<?php echo $queueOperator['online'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Online for the auto assignment') : erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Offline for the auto assignment'); ?>"><?php echo (int)$queueOperator['operator']['hide_online'] == 0 ? 'flash_on' : 'flash_off';?></span>
                    <?php if ($queueOperator['operator']['last_activity_age'] !== null) : ?><span class="small text-muted"><?php echo erLhcoreClassChat::formatSeconds($queueOperator['operator']['last_activity_age'])?></span><?php endif; ?>
                </td>
                <td><?php echo $queueOperator['operator']['last_accepted_age'] === null ? '-' : erLhcoreClassChat::formatSeconds($queueOperator['operator']['last_accepted_age'])?></td>
                <td><?php echo (int)$queueOperator['operator']['active_chats']?> / <?php echo (int)$queueOperator['operator']['pending_chats']?> / <?php echo (int)$queueOperator['operator']['inactive_chats']?></td>
                <td><?php echo (int)$queueOperator['operator']['max_chats'] == 0 ? '&infin;' : (int)$queueOperator['operator']['max_chats']?></td>
                <td><?php echo (int)$queueOperator['operator']['assign_priority']?></td>
                <td>
                    <?php if ($queueOperator['pickable']) : ?>
                        <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Would receive the next chat');?>">done</span>
                    <?php else : ?>
                        <?php foreach ($queueOperator['blocked_by'] as $queueBlockedBy) : ?><span class="badge bg-secondary me-1"><?php echo htmlspecialchars($queueBlockedBy)?></span> <?php endforeach; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Gates of the next chat');?></h6>
<ul class="list-unstyled small">
    <?php foreach ($autoAssignQueue['gates']['checks'] as $queueGate) : ?>
        <li>
            <span class="material-icons align-middle <?php echo $queueGate['allowed'] ? 'text-success' : 'text-danger';?>"><?php echo $queueGate['allowed'] ? 'check_circle' : 'cancel';?></span>
            <span class="badge bg-secondary"><?php echo htmlspecialchars($queueGate['requirement'])?></span>
            <?php echo htmlspecialchars($queueGate['details'])?>
        </li>
    <?php endforeach; ?>
</ul>
