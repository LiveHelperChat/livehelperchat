<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There are no online operators at the moment, please leave a message')?></h3>

<?php if (isset($request_send)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your request was sent!');?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php else : ?>
	<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?>/(offline)/true/(leaveamessage)/true<?php echo $append_mode?>" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

	<div class="row">
	    <div class="columns small-6 end">
	        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?>*</label>
	        <input type="text" class="inputfield" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
	    </div>
	    <div class="columns small-6 end">
	        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?>*</label>
	        <input type="text" class="inputfield" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
	    </div>
	</div>

	<?php if (isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) : ?>
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') : ?>*<?php endif;?></label>
	<input type="text" class="inputfield" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
	<?php endif; ?>

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?>*</label>
	<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>

	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>

	<input type="submit" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message');?>" name="StartChatAction" />
	<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>
	<input type="hidden" value="1" name="StartChat"/>

	</form>
<?php endif;?>

