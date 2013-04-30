<label>Name of manager:</label>
<input type="text" name="ManagerName" value="<?php echo htmlspecialchars($chatbox->chat->nick)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Please enter name of manager');?>">

<label>Chatbox name:</label>
<input type="text" name="ChatboxName" value="<?php echo htmlspecialchars($chatbox->name)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Chatbox name');?>">

<label>Identifier:</label>
<input type="text" name="Identifier" value="<?php echo htmlspecialchars($chatbox->identifier)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Identifier');?>">

<label><input type="checkbox" name="ActiveChatbox" value="on" <?php $chatbox->active == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Chatbox active');?></label>
