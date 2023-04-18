<div ng-non-bindable class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">

        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?> <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
	<?php echo htmlspecialchars($user->name)?>&nbsp;<?php echo htmlspecialchars($user->surname)?>
	<?php else : ?>
	-
<?php endif; ?><?php if ($chat->department != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->department)?><?php endif;?><?php if ($chat->product != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->product)?><?php endif;?>
</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        <div class="p-1 border-bottom">
            <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindowArchive('<?php echo $archive->id?>','<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>

            <i class="material-icons">label</i><small>ID - <?php echo $chat->id?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created')?> - <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?> - <?php echo $chat->chat_duration_front?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?> - <?php echo $chat->wait_time_front?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Priority')?> - (<?php echo $chat->priority?>)</small>

            <?php if ($chat->online_user_id > 0) : ?><i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor ID')?> - <?php echo $chat->online_user_id?></small><?php endif; ?>

            <?php foreach (erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id))) as $subject) : ?>
                <span class="badge bg-info fs12 me-1" ><?php echo htmlspecialchars($subject->subject)?></span>
            <?php endforeach; ?>
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

<div class="fs12">
<h6><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/previewchat','Last 100 messages rows');?></strong></h6>
<br/>
<?php $messages = array_reverse(erLhcoreClassModelChatArchiveMsg::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))); ?>
<?php if (isset($keyword) && !empty($keyword)) : ?>
    <?php foreach ($messages as $message) : ?>
        <?php $message->msg = preg_replace('/\b(' . preg_quote($keyword,'/') . ')\b/is','[level=bg-warning text-dark rounded p-1 d-inline-block][i][b][fs14]' . $keyword . '[/fs][/b][/i][/level]', $message->msg); ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>