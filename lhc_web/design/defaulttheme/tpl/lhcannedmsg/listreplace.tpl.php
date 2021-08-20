<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Replaceable variables');?></h1>

<br/>
<table class="table table-sm" cellpadding="0" cellspacing="0" ng-non-bindable>
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Identifier');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><?php echo htmlspecialchars($item->identifier)?></td>
            <td><?php echo htmlspecialchars(mb_substr($item->default,0,100))?></td>
            <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/editreplace')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit');?></a></td>
            <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/deletereplace')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a class="btn btn-sm btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/newreplace')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New');?></a>