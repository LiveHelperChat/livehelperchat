<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<h4>
	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?> <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
	<?php echo htmlspecialchars($user->name)?>&nbsp;<?php echo htmlspecialchars($user->surname)?>
	<?php else : ?>
	-
<?php endif; ?>
</h4>

		
<div class="p10 wb border-grey fs12">
<h5><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/previewchat','Last 100 messages rows');?></strong></h5>
<?php $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))); ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>