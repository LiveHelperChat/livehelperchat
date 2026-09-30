<?php if (!isset($explain['found']) || $explain['found'] !== true) : ?>
    <div class="alert alert-danger p-2 m-0"><?php echo htmlspecialchars($explain['error'])?></div>
<?php else : ?>
    <?php $operator = $explain['operator']; ?>
    <div class="alert <?php echo $explain['would_be_assigned_to_operator'] ? 'alert-success' : 'alert-danger'?> p-2 m-0 d-flex align-items-start">
        <span><span class="material-icons me-1"><?php echo $explain['would_be_assigned_to_operator'] ? 'check_circle' : 'cancel'?></span><?php echo htmlspecialchars($operator['answer'])?></span>
    </div>

    <div class="fs13 pt-2">
        <span class="badge bg-secondary">Chat #<?php echo (int)$explain['chat']['id']?></span>
        <span class="badge bg-secondary"><?php echo htmlspecialchars($explain['chat']['status_label'])?></span>
        <span class="badge bg-secondary">Priority <?php echo (int)$explain['chat']['priority']?></span>
        <span class="badge bg-secondary"><?php echo htmlspecialchars($explain['department']['label'])?></span>
        <?php if ($explain['chat']['chat_locale'] != '') : ?>
            <span class="badge bg-secondary"><?php echo htmlspecialchars($explain['chat']['chat_locale'])?></span>
        <?php endif;?>
        <?php if ($explain['chat']['assignee_user_id'] > 0) : ?>
            <span class="badge bg-secondary">Assigned: #<?php echo (int)$explain['chat']['assignee_user_id']?></span>
        <?php endif;?>
    </div>

    <div class="fs13 pt-2">
        <span class="text-muted">Workflow would pick:</span>
        <?php if ($explain['pick']['picked_user_id'] > 0) : ?>
            <b>#<?php echo (int)$explain['pick']['picked_user_id']?></b> <span class="text-muted">(<?php echo htmlspecialchars((string)$explain['pick']['variant'])?> variant)</span>
            <?php if ((int)$explain['pick']['picked_user_id'] !== (int)$operator['user_id']) : ?>
                <span class="badge bg-warning text-dark">not the operator you are testing</span>
            <?php endif;?>
        <?php else : ?>
            <b>nobody</b> <span class="text-muted">(every gate passes but no operator matches)</span>
        <?php endif;?>
    </div>

    <?php if (!$explain['gates']['allowed']) : ?>
        <div class="alert alert-warning p-2 mt-2 mb-0 fs13">
            <b>Department level gates block the auto assignment:</b> <?php echo htmlspecialchars($explain['gates_blocked_by_text'])?>.
            <table class="table table-sm table-borderless mb-0 mt-1 fs13">
                <tbody>
                <?php foreach ($explain['gates']['checks'] as $check) : if ($check['allowed']) { continue; } ?>
                    <tr>
                        <td width="1%"><span class="material-icons text-danger fs18">close</span></td>
                        <td nowrap="nowrap"><?php echo htmlspecialchars($check['requirement'])?></td>
                        <td>
                            <?php echo htmlspecialchars($check['details'])?>
                            <?php if ($check['explain'] != '') : ?>
                                <div class="text-muted"><?php echo htmlspecialchars($check['explain'])?></div>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    <?php endif;?>

    <?php if (isset($operator['blocked_by']) && !empty($operator['blocked_by'])) : ?>
        <div class="alert alert-danger p-2 mt-2 mb-0 fs13">
            <b>Blocked by:</b> <?php echo htmlspecialchars($operator['blocked_by_text'])?>.
            <div class="text-muted fs12 pt-1"><?php echo htmlspecialchars(implode(', ', $operator['blocked_by']))?></div>
        </div>
    <?php endif;?>

    <?php if (!empty($operator['assignment_rows_for_chat_department'])) : ?>
        <?php foreach ($operator['assignment_rows_for_chat_department'] as $assignmentRow) : ?>
            <fieldset class="border rounded p-2 mt-2 mb-2">
                <legend class="fs13 float-none w-auto mb-0 px-1">
                    <?php echo $assignmentRow['type'] == 0 ? 'Department assignment' : 'Department group assignment'?> #<?php echo (int)$assignmentRow['user_dep_id']?>
                    <?php if ($assignmentRow['pickable']) : ?>
                        <span class="badge bg-success">eligible</span>
                    <?php else : ?>
                        <span class="badge bg-danger">not eligible</span>
                    <?php endif;?>
                </legend>

                <table class="table table-sm table-striped mb-0">
                    <thead>
                    <tr>
                        <th width="1%">&nbsp;</th>
                        <th width="20%">Check</th>
                        <th>Details</th>
                        <th width="35%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($assignmentRow['checks'] as $check) : ?>
                        <tr>
                            <td>
                                <span class="material-icons fs18 <?php echo $check['allowed'] ? 'text-success' : 'text-danger'?>"><?php echo $check['allowed'] ? 'check' : 'close'?></span>
                            </td>
                            <td nowrap="nowrap">
                                <?php echo htmlspecialchars($check['requirement'])?>
                                <?php if (isset($check['group'])) : ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($check['group'])?></span>
                                <?php endif;?>
                            </td>
                            <td class="fs13"><?php echo htmlspecialchars($check['details'])?></td>
                            <td class="fs13 text-muted"><?php echo htmlspecialchars($check['explain'])?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>

                <div class="fs12 text-muted pt-1">
                    <b>Paths:</b>
                    default query - <?php echo $assignmentRow['paths']['default_variant'] ? 'yes' : 'no'?>,
                    chat priority queue - <?php echo $assignmentRow['paths']['priority_variant'] === null ? 'not applicable' : ($assignmentRow['paths']['priority_variant'] ? 'yes' : 'no')?>,
                    same language - <?php echo $assignmentRow['language_preferred'] ? 'yes' : 'no'?>
                </div>
            </fieldset>
        <?php endforeach;?>
    <?php endif;?>

    <div class="text-muted fs12">
        Read only explanation - nothing is written and no lock is taken. Extensions can replace the workflow through the
        <b>chat.workflow.autoassign</b> / <b>chat.workflow.autoassign_permit</b> events, those are not visible here.
    </div>
<?php endif;?>
