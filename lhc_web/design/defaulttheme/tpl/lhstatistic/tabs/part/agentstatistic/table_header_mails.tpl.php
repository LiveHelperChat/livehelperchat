<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Total mails');?></th>
<?php foreach (erLhcoreClassMailconvStatistic::getResponseTypes() as $item) : ?>
    <th>
        <?php echo htmlspecialchars($item->name)?>
    </th>
<?php endforeach; ?>