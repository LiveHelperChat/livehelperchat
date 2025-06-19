<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailing recipient');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/search_panel_mailing_recipient.tpl.php')); ?>

<table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','E-mail');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Attribute string 1');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Attribute string 2');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Attribute string 3');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Attribute string 4');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Attribute string 5');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Attribute string 6');?></th>
        <th width="1%"></th>
    </tr>
    </thead>
    <?php if (isset($items)) : foreach ($items as $item) : ?>
        <tr class="<?php if ($item->disabled == 1) : ?>text-muted<?php endif; ?>">
            <td>
                <i class="material-icons"><?php if ($item->disabled == 0) : ?>done<?php else : ?>block<?php endif; ?></i>
                <button data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Copied');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Click to copy');?>" class="mx-0 btn btn-xs btn-link text-muted py-1" data-copy="<?php echo htmlspecialchars($item->email)?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons mr-0">content_copy</i></button>
                <button class="m-0 p-0 btn btn-sm btn-link<?php if ($item->disabled == 1) : ?> text-muted<?php endif; ?>" href="#" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/editmailingrecipient')?>/<?php echo $item->id?>'})"><?php echo htmlspecialchars($item->email)?></button>

                <?php if ($item->mailbox != '') : ?>
                    <small class="pl-2 text-muted"><span class="material-icons">mail_outline</span><?php echo htmlspecialchars($item->mailbox)?></small>
                <?php endif; ?>

            </td>
            <td>
                <?php echo htmlspecialchars($item->name)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->attr_str_1)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->attr_str_2)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->attr_str_3)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->attr_str_4)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->attr_str_5)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->attr_str_6)?>
            </td>
            <td>
                <a class="csfr-post csfr-required text-danger" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/deleterecipient')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
            </td>
        </tr>
    <?php endforeach;endif; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>