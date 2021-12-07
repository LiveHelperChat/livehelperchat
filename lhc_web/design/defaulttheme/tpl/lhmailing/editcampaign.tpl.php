<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Edit');?></h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>



<form action="<?php echo erLhcoreClassDesign::baseurl('mailing/editcampaign')?>/<?php echo $item->id?>" method="post">

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li role="presentation" class="nav-item"><a href="#settings" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="settings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Main');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_statistic') : ?> active<?php endif;?>" href="#statistic" aria-controls="options" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Statistic');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="settings">
            <?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/form_campaign.tpl.php'));?>
        </div>
        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_statistic') : ?>active<?php endif;?>" id="statistic">
            Statistic
        </div>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-sm btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-sm btn-secondary" name="Update_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
        <input type="submit" class="btn btn-sm btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>