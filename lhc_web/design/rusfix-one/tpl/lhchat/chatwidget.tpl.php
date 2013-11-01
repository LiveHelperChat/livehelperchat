<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if ($leaveamessage == false || erLhcoreClassChat::isOnline($department) === true) : ?>
<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?><?php echo $append_mode?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $input_data->priority !== false ? print '/(priority)/'.$input_data->priority : ''?><?php $input_data->vid !== false ? print '/(vid)/'.htmlspecialchars($input_data->vid) : ''?><?php $input_data->hash_resume !== false ? print '/(hash_resume)/'.htmlspecialchars($input_data->hash_resume) : ''?>" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

<div class="row">
    <?php if (isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true) : ?>
    <div class="columns small-6 end">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') : ?>*<?php endif;?></label>
        <input type="text" class="inputfield" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" placeholder="Ваше имя"/>
    </div>
    <?php endif; ?>

    <?php if (isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) : ?>
    <div class="columns small-6 end">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?><?php if (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required') : ?>*<?php endif;?></label>
        <input type="text" class="inputfield" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
    </div>
    <?php endif; ?>
</div>

<?php if (isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') : ?>*<?php endif;?></label>
<input type="text" class="inputfield" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php endif; ?>

<?php if (isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?><?php if (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required') : ?>*<?php endif;?></label>
<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php endif; ?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php if ($department === false) : ?>
	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>
<?php endif;?>



<center>
  <input type="submit" class="button"  style="font-size: 15px; padding: 15px;" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChatAction" />
  &nbsp;&nbsp;
  <?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly()) !== false ) : ?>
    <input type="button" class="success button"  style="font-size: 15px; padding: 15px;" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?>" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $reopenData['id']?>/<?php echo $reopenData['hash']?>/(mode)/widget<?php if ( isset($append_mode) && $append_mode != '' ) : ?>/(embedmode)/embed<?php endif;?>'">
  <?php endif; ?>
</center>
<!--
<ul class="button-group">
  <li><input type="submit" class="button"  style="font-size: 15px; padding: 10px; float: left;" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChatAction" /></li>
  <?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly()) !== false ) : ?>
  <li><input type="button" class="success button"  style="font-size: 15px; padding: 10px; float: right;" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?>" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $reopenData['id']?>/<?php echo $reopenData['hash']?>/(mode)/widget<?php if ( isset($append_mode) && $append_mode != '' ) : ?>/(embedmode)/embed<?php endif;?>'"></li>
  <?php endif; ?>
</ul>
-->
<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer" />
<input type="hidden" value="<?php echo htmlspecialchars($referer_site);?>" name="r" />
<input type="hidden" value="1" name="StartChat"/>

</form>

<?php else : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/offline_form.tpl.php'));?>
<?php endif;?>