<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Chatbox name');?>:</label>
<input type="text" name="ChatboxName" value="<?php echo htmlspecialchars($chatbox->name)?>" >

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Name of manager');?>:</label>
<input type="text" name="ManagerName" value="<?php echo htmlspecialchars($chatbox->chat->nick)?>" >

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Identifier');?>:</label>
<input type="text" name="Identifier" value="<?php echo htmlspecialchars($chatbox->identifier)?>" >

<label><input type="checkbox" name="ActiveChatbox" value="on" <?php $chatbox->active == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Chatbox active');?></label>
