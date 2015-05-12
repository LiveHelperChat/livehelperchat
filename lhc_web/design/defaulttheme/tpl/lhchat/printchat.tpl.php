<div class="fs12">
<h2><?php echo htmlspecialchars($chat->nick)?><?php $chat->city != '' ? print ', '.htmlspecialchars($chat->city) : ''?>, <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?> <div class="right">IP:<?php echo $chat->ip?>, ID: <?php echo $chat->id?><?php if ($chat->fbst == 1) : ?> <i class="icon-thumbs-up up-voted"></i><?php elseif ($chat->fbst == 2) : ?> <i class="icon-thumbs-down down-voted"><?php endif;?></div></h2>
<?php $messages = erLhcoreClassModelmsg::getList(array('limit' => 1000,'sort' => 'id ASC','filter' => array('chat_id' => $chat->id))); ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list.tpl.php'));?>
</div>