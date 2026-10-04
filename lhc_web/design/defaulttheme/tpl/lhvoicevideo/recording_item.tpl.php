<?php $trRec = erTranslationClassLhTranslation::getInstance(); ?>
<div class="fs13 text-nowrap">
<?php if ($recording->status == erLhcoreClassModelChatVoiceVideoRecording::STATUS_COMPLETED) : ?>
    <span class="material-icons text-success"><?php echo $recording->audio_only == 1 ? 'mic' : 'videocam'?></span>
    <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/recording')?>/<?php echo $recording->id?>" title="<?php echo $trRec->getTranslation('chat/voice_video','Play')?>"><span class="material-icons">play_circle</span><?php echo $recording->duration_front?></a>
    <a href="<?php echo erLhcoreClassDesign::baseurl('voicevideo/recording')?>/<?php echo $recording->id?>/(download)/1" title="<?php echo $trRec->getTranslation('chat/voice_video','Download')?> (<?php echo $recording->file_size_front?>)"><span class="material-icons">file_download</span></a>
<?php elseif ($recording->is_active) : ?>
    <span class="text-danger"><span class="material-icons">fiber_manual_record</span><?php echo $trRec->getTranslation('chat/voice_video','Recording')?></span>
<?php else : ?>
    <span class="text-muted" title="<?php echo htmlspecialchars($recording->error)?>"><span class="material-icons">error_outline</span><?php echo $trRec->getTranslation('chat/voice_video','Recording failed')?></span>
<?php endif; ?>
</div>
