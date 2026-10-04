<?php $trans = erTranslationClassLhTranslation::getInstance(); ?>
<h1><?php echo $trans->getTranslation('chat/voice_video','Call history')?></h1>

<?php if (isset($db_error)) : $errors = array($trans->getTranslation('chat/voice_video','Call history table is missing. Please run database update.')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form ng-non-bindable action="<?php echo erLhcoreClassDesign::baseurl('voicevideo/sessions')?>" method="get" class="pb-2" autocomplete="off">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Date from');?></label>
                <input type="date" class="form-control form-control-sm" name="date_from" value="<?php echo htmlspecialchars($input->date_from)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Date to');?></label>
                <input type="date" class="form-control form-control-sm" name="date_to" value="<?php echo htmlspecialchars($input->date_to)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Department');?></label>
                <select name="dep_id" class="form-control form-control-sm">
                    <option value=""><?php echo $trans->getTranslation('chat/voice_video','Any');?></option>
                    <?php foreach (erLhcoreClassModelDepartament::getList(array('limit' => false, 'sort' => 'name ASC')) as $department) : ?>
                        <option value="<?php echo $department->id?>" <?php (string)$input->dep_id === (string)$department->id ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($department->name)?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Operator');?></label>
                <select name="user_id" class="form-control form-control-sm">
                    <option value=""><?php echo $trans->getTranslation('chat/voice_video','Any');?></option>
                    <?php foreach (erLhcoreClassModelUser::getList(array('limit' => 1000, 'sort' => 'name ASC')) as $user) : ?>
                        <option value="<?php echo $user->id?>" <?php (string)$input->user_id === (string)$user->id ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($user->name_official)?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Status');?></label>
                <select name="status" class="form-control form-control-sm">
                    <option value=""><?php echo $trans->getTranslation('chat/voice_video','Any');?></option>
                    <option value="<?php echo erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED?>" <?php $input->status === erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED ? print 'selected="selected"' : ''?>><?php echo $trans->getTranslation('chat/voice_video','Completed');?></option>
                    <option value="<?php echo erLhcoreClassModelChatVoiceVideoSession::STATUS_MISSED?>" <?php $input->status === erLhcoreClassModelChatVoiceVideoSession::STATUS_MISSED ? print 'selected="selected"' : ''?>><?php echo $trans->getTranslation('chat/voice_video','Not answered');?></option>
                    <option value="<?php echo erLhcoreClassModelChatVoiceVideoSession::STATUS_ACTIVE?>" <?php $input->status === erLhcoreClassModelChatVoiceVideoSession::STATUS_ACTIVE ? print 'selected="selected"' : ''?>><?php echo $trans->getTranslation('chat/voice_video','In progress');?></option>
                    <option value="<?php echo erLhcoreClassModelChatVoiceVideoSession::STATUS_RINGING?>" <?php $input->status === erLhcoreClassModelChatVoiceVideoSession::STATUS_RINGING ? print 'selected="selected"' : ''?>><?php echo $trans->getTranslation('chat/voice_video','Ringing');?></option>
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Type');?></label>
                <select name="video" class="form-control form-control-sm">
                    <option value=""><?php echo $trans->getTranslation('chat/voice_video','Any');?></option>
                    <option value="0" <?php $input->video === 0 ? print 'selected="selected"' : ''?>><?php echo $trans->getTranslation('chat/voice_video','Voice');?></option>
                    <option value="1" <?php $input->video === 1 ? print 'selected="selected"' : ''?>><?php echo $trans->getTranslation('chat/voice_video','Video');?></option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $trans->getTranslation('chat/voice_video','Chat ID');?></label>
                <input type="text" class="form-control form-control-sm" name="chat_id" value="<?php echo htmlspecialchars($input->chat_id)?>" />
            </div>
        </div>
    </div>
    <div class="btn-group" role="group">
        <input type="submit" class="btn btn-sm btn-secondary" value="<?php echo $trans->getTranslation('chat/lists/search_panel','Search');?>" />
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/sessions')?>?<?php echo htmlspecialchars($appendQuery . ($appendQuery != '' ? '&' : '') . 'export=csv')?>"><span class="material-icons">file_download</span>CSV</a>
    </div>
</form>

<?php if (isset($stats) && $stats['total'] > 0) : ?>
<div class="row pb-2">
    <div class="col"><div class="border rounded p-2"><div class="text-muted fs13"><?php echo $trans->getTranslation('chat/voice_video','Calls')?></div><b><?php echo (int)$stats['total']?></b></div></div>
    <div class="col"><div class="border rounded p-2"><div class="text-muted fs13"><?php echo $trans->getTranslation('chat/voice_video','Completed')?></div><b><?php echo (int)$stats['answered']?></b></div></div>
    <div class="col"><div class="border rounded p-2"><div class="text-muted fs13"><?php echo $trans->getTranslation('chat/voice_video','Not answered')?></div><b><?php echo (int)$stats['missed']?></b></div></div>
    <div class="col"><div class="border rounded p-2"><div class="text-muted fs13"><?php echo $trans->getTranslation('chat/voice_video','Total talk time')?></div><b><?php echo erLhcoreClassVoiceVideo::formatDuration($stats['talk_time'])?></b></div></div>
    <div class="col"><div class="border rounded p-2"><div class="text-muted fs13"><?php echo $trans->getTranslation('chat/voice_video','Average duration')?></div><b><?php echo erLhcoreClassVoiceVideo::formatDuration($stats['avg_duration'])?></b></div></div>
    <div class="col"><div class="border rounded p-2"><div class="text-muted fs13"><?php echo $trans->getTranslation('chat/voice_video','Average wait time')?></div><b><?php echo erLhcoreClassVoiceVideo::formatDuration($stats['avg_wait'])?></b></div></div>
</div>
<?php endif; ?>

<?php if (isset($items)) : ?>
    <table class="table table-sm" ng-non-bindable cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th width="1%">ID</th>
            <th width="1%" nowrap><?php echo $trans->getTranslation('chat/voice_video','Chat ID');?></th>
            <th><?php echo $trans->getTranslation('chat/voice_video','Type');?></th>
            <th><?php echo $trans->getTranslation('chat/voice_video','Operator');?></th>
            <th><?php echo $trans->getTranslation('chat/voice_video','Department');?></th>
            <th><?php echo $trans->getTranslation('chat/voice_video','Status');?></th>
            <th nowrap><?php echo $trans->getTranslation('chat/voice_video','Started');?></th>
            <th nowrap><?php echo $trans->getTranslation('chat/voice_video','Wait time');?></th>
            <th nowrap><?php echo $trans->getTranslation('chat/voice_video','Duration');?></th>
            <th nowrap><?php echo $trans->getTranslation('chat/voice_video','End reason');?></th>
            <?php if (!empty($recordings) || erLhcoreClassUser::instance()->hasAccessTo('lhvoicevideo','recordings')) : ?><th nowrap><?php echo $trans->getTranslation('chat/voice_video','Recording');?></th><?php endif; ?>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/single')?>/<?php echo $item->chat_id?>"><?php echo $item->chat_id?></a></td>
                <td nowrap>
                    <span class="material-icons" title="<?php echo htmlspecialchars($item->provider)?>"><?php echo $item->video == 1 ? 'videocam' : 'call'?></span>
                    <span class="material-icons text-muted" title="<?php echo $item->initiator == erLhcoreClassModelChatVoiceVideoSession::INITIATOR_OPERATOR ? $trans->getTranslation('chat/voice_video','Started by operator') : $trans->getTranslation('chat/voice_video','Started by visitor')?>"><?php echo $item->initiator == erLhcoreClassModelChatVoiceVideoSession::INITIATOR_OPERATOR ? 'support_agent' : 'face'?></span>
                </td>
                <td><?php echo $item->user instanceof erLhcoreClassModelUser ? htmlspecialchars($item->user->name_official) : '-'?></td>
                <td><?php echo $item->department instanceof erLhcoreClassModelDepartament ? htmlspecialchars($item->department->name) : '-'?></td>
                <td nowrap>
                    <span class="badge <?php if ($item->status == erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED) : ?>bg-success<?php elseif ($item->status == erLhcoreClassModelChatVoiceVideoSession::STATUS_MISSED) : ?>bg-danger<?php else : ?>bg-warning<?php endif; ?>"><?php echo htmlspecialchars($item->status_front)?></span>
                </td>
                <td nowrap><?php echo $item->ctime_front?></td>
                <td nowrap><?php echo $item->answered_at > 0 ? erLhcoreClassVoiceVideo::formatDuration($item->wait_time) : '-'?></td>
                <td nowrap><?php echo $item->status == erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED ? $item->duration_front : '-'?></td>
                <td nowrap class="text-muted fs13"><?php echo htmlspecialchars($item->end_reason)?></td>
                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhvoicevideo','recordings')) : ?>
                <td nowrap>
                    <?php if (isset($recordings[$item->id])) : foreach ($recordings[$item->id] as $recording) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhvoicevideo/recording_item.tpl.php'));?>
                    <?php endforeach; endif; ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php elseif (!isset($db_error)) : ?>
    <p class="text-muted"><?php echo $trans->getTranslation('chat/voice_video','No calls found');?></p>
<?php endif; ?>
