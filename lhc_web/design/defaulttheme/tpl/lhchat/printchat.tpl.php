<div class="fs12">
<h4><?php echo htmlspecialchars($chat->nick)?><?php $chat->city != '' ? print ', '.htmlspecialchars($chat->city) : ''?>, <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?> <div class="float-right">IP:<?php echo htmlspecialchars($chat->ip)?>, ID: <?php echo $chat->id?><?php if ($chat->fbst == 1) : ?> <i class="material-icons up-voted">thumb_up</i><?php elseif ($chat->fbst == 2) : ?> <i class="material-icons down-voted">thumb_down<i><?php endif;?></div></h4>
<?php $messages = erLhcoreClassModelmsg::getList(array('limit' => 1000,'sort' => 'id ASC','filter' => array('chat_id' => $chat->id))); ?>

    <div class="msgBlock msgBlock-admin" style="height: auto;">
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list.tpl.php'));?>
    </div>
</div>