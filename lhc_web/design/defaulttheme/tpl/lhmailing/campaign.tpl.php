<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Campaigns list');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/search_panel_mailinglist.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Status');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/editcampaign')?>/<?php echo $item->id?>" ><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>" >List of recipients</a>
                </td>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('mailing/deletecampaign')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
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

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/newcampaign')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>