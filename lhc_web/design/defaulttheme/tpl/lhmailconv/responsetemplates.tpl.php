<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','Response templates');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/search_panel.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','Name');?></th>
            <?php if ($currentUser->hasAccessTo('lhmailconv','rtemplates_manage')) : ?><th width="1%"></th><?php endif; ?>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php if ($currentUser->hasAccessTo('lhmailconv','rtemplates_manage')) : ?>
                        <a class="action-image<?php if ($item->disabled == 1) :?> text-muted<?php endif;?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'mailconv/previewresponsetemplate/<?php echo $item->id?>'});"><span class="material-icons">visibility</span></a><?php if ($item->disabled == 1) :?><i class="text-danger material-icons">block</i><?php endif;?><a <?php if ($item->disabled == 1) :?>class="text-muted"<?php endif;?> href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editresponsetemplate')?>/<?php echo $item->id?>" ><?php echo htmlspecialchars($item->name)?></a>
                    <?php else : ?>
                         <a class="action-image<?php if ($item->disabled == 1) :?> text-muted<?php endif;?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'mailconv/previewresponsetemplate/<?php echo $item->id?>'});"><span class="material-icons">visibility</span><?php if ($item->disabled == 1) :?><i class="text-danger material-icons">block</i><?php endif;?><?php echo htmlspecialchars($item->name)?></a>
                    <?php endif; ?>
                </td>
                <?php if ($currentUser->hasAccessTo('lhmailconv','rtemplates_manage')) : ?>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editresponsetemplate')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE254;</i></a>
                        <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deleteresponsetemplate')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE872;</i></a>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>
<?php if ($currentUser->hasAccessTo('lhmailconv','rtemplates_manage')) : ?>
<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/newresponsetemplate')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
<?php endif; ?>