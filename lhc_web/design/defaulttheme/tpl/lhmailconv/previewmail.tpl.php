<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 pl-2 pr-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span><?php echo htmlspecialchars($chat->subject)?> <?php $user = $chat->user;  if ($user !== false) : ?><?php if ($chat->department_name != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->department_name)?><?php endif;?><?php endif;?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="p-1 border-bottom">
            <i class="material-icons">label</i><small>ID - <?php echo $chat->id?></small>&nbsp;
            <i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvpreview','Created')?> - <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->pnd_time)?></small>&nbsp;
            <?php if ($chat->accept_time > 0) : ?>
            <i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvpreview','Wait response time')?> - <?php echo $chat->wait_time_response?></small>&nbsp;
            <?php endif; ?>
            <i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvpreview','Wait time')?> - <?php echo $chat->wait_time_pending?></small>&nbsp;
            <i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvpreview','Priority')?> - (<?php echo $chat->priority?>)</small>
            <i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvpreview','User')?> - (<?php echo htmlspecialchars($chat->plain_user_name)?>)</small>
        </div>
        <div class="modal-body">
            <div id="chat-id-previewmc<?php echo $chat->id?>"></div>
            <script>
                ee.emitEvent('mailChatTabLoaded', ['mc<?php echo $chat->id?>','preview']);
            </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>