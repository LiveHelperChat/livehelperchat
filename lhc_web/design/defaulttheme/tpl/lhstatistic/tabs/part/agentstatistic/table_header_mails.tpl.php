<th>
    <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/agent_stat_total_mails'});" class="material-icons text-muted">help</a>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Total mails');?>
</th>
<?php foreach (erLhcoreClassMailconvStatistic::getResponseTypes() as $item) : ?>
    <th>
        <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/agent_stat_mails_<?php echo $item->id?>'});" class="material-icons text-muted">help</a>
        <?php echo htmlspecialchars($item->name)?>
   </th>
<?php endforeach; ?>