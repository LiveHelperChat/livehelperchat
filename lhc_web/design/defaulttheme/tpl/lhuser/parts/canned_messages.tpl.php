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

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title/Message');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></th>    
    <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_column_multiinclude.tpl.php'));?>    
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($cannedMessages as $message) : ?>
    <tr>
        <td><?php echo $message->id?></td>
        <td><?php echo nl2br(htmlspecialchars($message->title != '' ? $message->title : $message->msg))?></td>
        <td><?php echo $message->delay?></td>
        <td><?php echo $message->position?></td>
        <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_column_content_multiinclude.tpl.php'));?>
        <td nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(msg)/<?php echo $message->id?>/(tab)/canned"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit message');?></a></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(action)/delete/(tab)/canned/(msg)/<?php echo $message->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete message');?></a></td>
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
    
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title');?></label>
        <input type="text" class="form-control" name="Title" value="<?php echo htmlspecialchars($canned_msg->title);?>" />
    </div>
    
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Explain');?></label>
        <input type="text" class="form-control" name="ExplainHover" value="<?php echo htmlspecialchars($canned_msg->explain);?>" />
    </div>
    
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("chat/cannedmsg","Tag's");?></label>
        <input type="text" class="form-control" name="Tags" value="<?php echo htmlspecialchars($canned_msg->tags_plain)?>" />
    </div>

    <div class="form-group">
	    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?></label>
        <textarea class="form-control" name="Message"><?php echo htmlspecialchars($canned_msg->msg);?></textarea>
    </div>
    
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
        <textarea class="form-control" name="FallbackMessage"><?php echo htmlspecialchars($canned_msg->fallback_msg);?></textarea>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="AutoSend" value="on" <?php $canned_msg->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>
    </div>
    
    <div class="form-group">
	     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
        <input type="text" class="form-control" name="Delay" value="<?php echo $canned_msg->delay?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></label>
        <input type="text" class="form-control" name="Position" value="<?php echo $canned_msg->position?>" />
    </div>
 	    
 	<?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_fields_multiinclude.tpl.php'));?>  
 	    
	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" class="btn btn-default" name="Save_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    	<?php if ($canned_msg->id > 0) : ?>
    	<input type="submit" class="btn btn-default" name="Cancel_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    	<?php endif;?>
	</div>
	
</form>