<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','List');?></h1>

<table class="twelve" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Identifier');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Explain');?></th>
    <th width="10%">&nbsp;</th>
</tr>
</thead>
<?php foreach (erLhcoreClassModelChatConfig::getItems() as $item) : ?>
    <tr>
        <td><?php echo $item->identifier?></td>
        <td><?php echo htmlspecialchars($item->explain)?></td>
        <td><a class="tiny button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/<?php echo $item->identifier?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Edit value');?></a></td>
    </tr>
<?php endforeach; ?>
</table>