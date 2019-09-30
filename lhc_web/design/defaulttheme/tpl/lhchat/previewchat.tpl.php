<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header pt-1 pb-1 pl-2 pr-2">

        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?> <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>

	<?php echo htmlspecialchars($user->name)?>&nbsp;<?php echo htmlspecialchars($user->surname)?>
	<?php else : ?>
	-
<?php endif; ?><?php if ($chat->department != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->department)?><?php endif;?><?php if ($chat->product != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->product)?><?php endif;?>
</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          
      </div>
        <div class="p-1 border-bottom">
            <i class="material-icons">label</i><small>ID - <?php echo $chat->id?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created')?> - <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?> - <?php echo $chat->chat_duration_front?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?> - <?php echo $chat->wait_time_front?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Priority')?> - (<?php echo $chat->priority?>)</small>
        </div>
      <div class="modal-body">

<small id="preview-messages-<?php echo $chat->id?>">
    <?php $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
</small>

  <script>
      (function(chatId, msgId){
          var currentChatId = chatId;
          var currentLastMessageID = msgId;
          function updatePreviewLive() {
              $.postJSON(WWW_DIR_JAVASCRIPT + 'chat/syncadmin' ,{ 'chats[]': [currentChatId + ',' + currentLastMessageID]}, function(data) {
                  if (data.result != 'false') {
                      $.each(data.result, function (i, item) {
                          currentLastMessageID = item.message_id;
                          $('#preview-messages-' + item.chat_id).append(item.content);
                      });
                  }
                  if ($('#preview-messages-'+currentChatId).is(':visible') == true){
                      setTimeout(function () {
                          updatePreviewLive();
                      },2000);
                  }
              });
          }
          setTimeout(function () {
              updatePreviewLive();
          },5000);
      })(<?php echo $chat->id,',',$msg->id?>);
  </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>