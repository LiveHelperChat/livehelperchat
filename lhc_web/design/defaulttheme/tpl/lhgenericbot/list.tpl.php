<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/search_panel.tpl.php')); ?>

<?php if (isset($items)) : ?>

<table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Name');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td>
                <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/bot_list_item_name.tpl.php'));?>
            </td>
            <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
            <td>
                <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/delete')?>/<?php echo $item->id?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php endif; ?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>
<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/import')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Import')?></a>
