<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/previewchat','Last 100 messages rows');?></h2>

<?php $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))); ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list.tpl.php'));?>
<br>