<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chat');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Stars');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Time');?></th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->chat_id)?></td>
    	<td><?php echo htmlspecialchars($item->department_name)?></td>
    	<td><?php echo htmlspecialchars($item->user)?></td>
    	<td><?php echo htmlspecialchars($item->stars)?></td>
    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->ftime_front)?></td>
    </tr>
<?php endforeach; ?>
</table>
