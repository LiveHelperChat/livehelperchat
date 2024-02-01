<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span><?php echo htmlspecialchars($chat->subject)?> <?php $user = $chat->user;  if ($user !== false) : ?><?php if ($chat->department_name != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->department_name)?><?php endif;?><?php endif;?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

        <?php if (isset($_GET['prevId']) || isset($_GET['nextId'])) : ?>
            <div class="p-1 border-bottom">
                <button type="button" <?php if (isset($_GET['prevId'])) : ?>onclick="$('#preview-item-<?php echo (int)$_GET['prevId']?>').click()"<?php else : ?>disabled="disabled"<?php endif; ?> class="btn btn-xs btn-secondary"><span class="material-icons fs13 me-0">arrow_back_ios</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Previous item')?></button>&nbsp;<button type="button" <?php if (isset($_GET['nextId'])) : ?>onclick="$('#preview-item-<?php echo (int)$_GET['nextId']?>').click()"<?php else : ?>disabled="disabled"<?php endif; ?> class="btn btn-xs btn-secondary" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Next item')?><span class="material-icons me-0 fs13">arrow_forward_ios</span></button>
                <span class="text-muted fs13 ps-1">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Use Alt+↑↓ arrows to navigate in the list.')?>
                </span>

            </div>
        <?php endif; ?>

        <div class="modal-body mx550">
            <div id="chat-id-previewmc<?php echo $chat->id?>"></div>
            <script>
                ee.emitEvent('mailChatTabLoaded', ['mc<?php echo $chat->id?>','preview',false,<?php echo json_encode($keyword)?>]);
            </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>