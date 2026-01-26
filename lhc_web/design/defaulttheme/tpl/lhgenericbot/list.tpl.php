<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/search_panel.tpl.php')); ?>

<?php if (isset($items)) : ?>

<table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
    <thead>
    <tr>
        <th width="45%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Name');?></th>
        <th width="45%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Short Name');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td>
                <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/bot_list_item_name.tpl.php'));?>
            </td>
            <td>
                <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/bot_list_item_short_name.tpl.php'));?>
            </td>
            <td nowrap>
                <button type="button" data-attr-id="<?php echo $item->id?>" class="btn btn-secondary btn-xs btn-use-cases" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Investigate places where this bot is used');?>"><span class="material-icons fs12">action_key</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Use cases');?></button>
            </td>
            <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
            <td>
                <a class="btn btn-danger btn-xs csfr-post csfr-required" data-ajax-confirm="true" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/delete')?>/<?php echo $item->id?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a>
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


<script>
    $('.btn-use-cases').click(function(){
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/usecases/bot/'+$(this).data('attr-id')});
    });
</script>