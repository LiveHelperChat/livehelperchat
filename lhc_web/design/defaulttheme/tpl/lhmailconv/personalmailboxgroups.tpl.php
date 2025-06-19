<h1>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Personal Mailbox Rules');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/personalmailbox'});" class="material-icons text-muted">help</a>
</h1>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mails');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Active');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php if ($item->failed == 1) : ?>
                        <i class="material-icons text-danger">warning</i>
                    <?php endif; ?>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editpersonalmailboxgroup')?>/<?php echo $item->id?>" ><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td>
                    <?php echo htmlspecialchars($item->mails)?>
                </td>
                <td>
                    <?php if ($item->active == 1) : ?>
                        <i title="Ok" class="material-icons chat-active">&#xE5CA;</i>
                    <?php else : ?>
                        <i title="Blocked" class="material-icons chat-closed">&#xE14B;</i>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editpersonalmailboxgroup')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE254;</i></a>
                        <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deletemailbox')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE872;</i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/newpersonalmailboxgroup')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>