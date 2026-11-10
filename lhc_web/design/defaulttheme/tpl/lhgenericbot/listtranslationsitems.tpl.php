<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Translations items')?></h1>

<?php if (isset($items)) : ?>
    <table class="table table-sm" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Identifier');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <a title="<?php echo $item->id?>" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edittritem')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->identifier)?></a>
                    <?php if ($item->auto_translate == 1) : ?>
                        <span class="badge bg-info" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Automatic translation enabled')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Auto translate')?></span>
                    <?php endif; ?>
                    <?php if ($item->detect_language == 1) : ?>
                        <span class="badge bg-warning" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Chat language detection enabled')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Detect language')?></span>
                    <?php endif; ?>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edittritem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/deletetritem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<div class="btn-group" role="group" aria-label="...">
    <a class="btn btn-sm btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/newtritem')?><?php if (isset($input->group_id) && $input->group_id > 0) : ?>/(group_id)/<?php echo (int)$input->group_id?><?php endif; ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>

    <?php if (isset($pages)) : ?>
        <button type="button" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions')?>', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>?quick_action=1'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">sync_alt</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions')?></button>
    <?php endif; ?>
</div>