<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Chatbox name');?>:</label>
<input class="form-control" type="text" name="ChatboxName" value="<?php echo htmlspecialchars($chatbox->name)?>" >
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Name of manager');?>:</label>
<input class="form-control" type="text" name="ManagerName" value="<?php echo htmlspecialchars($chatbox->chat->nick)?>" >
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Identifier');?>:</label>
<input class="form-control" type="text" name="Identifier" value="<?php echo htmlspecialchars($chatbox->identifier)?>" >
</div>

<div class="form-group">
<label><input type="checkbox" name="ActiveChatbox" value="on" <?php $chatbox->active == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/form','Chatbox active');?></label>
</div>