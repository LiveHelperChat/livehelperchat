<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Matching rules');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel_matching_rule.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Priority');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Conversation priority');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Conditions');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Active');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Department');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo htmlspecialchars($item->id)?></td>
                <td><?php echo htmlspecialchars($item->name)?></td>
                <td><?php echo htmlspecialchars($item->priority_rule)?></td>
                <td><?php echo htmlspecialchars($item->priority)?></td>
                <td>
                    <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','TO');?>:</b> <?php echo erLhcoreClassDesign::shrt(implode(', ',$item->mailbox_object_ids),100,'...',30,ENT_QUOTES)?><br>
                    <?php if ($item->from_name != '') : ?><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','From name matches');?></b>: <?php echo erLhcoreClassDesign::shrt(str_replace('  ',' ',str_replace(',',', ',$item->from_name)),100,'...',30,ENT_QUOTES)?><br/><?php endif; ?>
                    <?php if ($item->subject_contains != '') : ?><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Subject contains');?></b>: <?php echo erLhcoreClassDesign::shrt(str_replace('  ',' ',str_replace(',',', ',$item->subject_contains)),100,'...',30,ENT_QUOTES)?><br/><?php endif; ?>
                    <?php if ($item->from_mail != '') : ?><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','From mail');?></b>: <?php echo erLhcoreClassDesign::shrt(str_replace('  ',' ',str_replace(',',', ',$item->from_mail)),100,'...',30,ENT_QUOTES)?><br/><?php endif; ?>
                </td>
                <td>
                    <?php if ($item->active == 1) : ?>
                        <i title="Ok" class="material-icons chat-active">&#xE5CA;</i>
                    <?php else : ?>
                        <i title="Blocked" class="material-icons chat-closed">&#xE14B;</i>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($item->department)?></td>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmatchrule')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE254;</i></a>
                        <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deletematchingrule')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE872;</i></a>
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

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/newmatchrule')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>