<th></th>
<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Total number of chats');?></th>
<?php if (is_array($input->subject_ids) && !empty($input->subject_ids)) : ?>
    <?php foreach ($input->subject_ids as $subjectId) : ?>
        <th class="fs12" nowrap="">
            <span class="material-icons">label</span><?php echo htmlspecialchars(erLhAbstractModelSubject::fetch($subjectId));?>
        </th>
    <?php endforeach; ?>
<?php endif; ?>
<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Number of chats while online');?></th>
<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hours on chat (sum of chat duration)');?></th>
<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Time online (sum of time spend online)');?></th>
<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','AVG number of chats per hour');?></th>
<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average pick-up time');?></th>
<th><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_duration'});" class="material-icons text-muted">help</a><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average chat length');?></th>
<th><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_frt'});" class="material-icons text-muted">help</a><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average of first response time (agent)');?></th>
<th><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_aart'});" class="material-icons text-muted">help</a><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average of response time (agent)');?></th>
<th><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_mart'});" class="material-icons text-muted">help</a><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average of maximum response time (agent)');?></th>