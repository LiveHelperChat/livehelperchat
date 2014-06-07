<?php 
$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/account').'/(tab)/canned';
$pages->items_total = erLhcoreClassModelCannedMsg::getCount(array('filter' => array('user_id' => $user->id)));
$pages->setItemsPerPage(10);
$pages->paginate();

$cannedMessages = array();
if ($pages->items_total > 0) {
    $cannedMessages = erLhcoreClassModelCannedMsg::getList(array('filter' => array('user_id' => $user->id),'offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

?>

<table class="twelve" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($cannedMessages as $message) : ?>
    <tr>
        <td><?php echo $message->id?></td>
        <td><?php echo nl2br(htmlspecialchars($message->msg))?></td>
        <td><?php echo $message->delay?></td>
        <td><?php echo $message->position?></td>
        <td nowrap><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(msg)/<?php echo $message->id?>#canned"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit message');?></a></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required small alert button round" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(action)/delete/(tab)/canned/(msg)/<?php echo $message->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete message');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<hr>

<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Personal canned message');?></h3>

<?php if (isset($errors_canned)) : $errors = $errors_canned; ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated_canned)) : ?>
		<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned message was saved'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php if ($canned_msg->id > 0) : ?><?php echo erLhcoreClassDesign::baseurl('user/account')?>/(msg)/<?php echo $canned_msg->id?><?php endif;?>#canned" method="post">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?></label>
    <textarea name="Message"><?php echo htmlspecialchars($canned_msg->msg);?></textarea>

    <label><input type="checkbox" name="AutoSend" value="on" <?php $canned_msg->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>
    
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
    <input type="text" name="Delay" value="<?php echo $canned_msg->delay?>" />

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></label>
    <input type="text" name="Position" value="<?php echo $canned_msg->position?>" />
 
    <ul class="button-group radius">
    	<li><input type="submit" class="small button" name="Save_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
    	<?php if ($canned_msg->id > 0) : ?>
    	<li><input type="submit" class="small button" name="Cancel_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
    	<?php endif;?>
	</ul>
</form>