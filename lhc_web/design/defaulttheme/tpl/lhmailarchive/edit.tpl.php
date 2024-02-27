<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Edit archive');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Archive updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form ng-non-bindable action="<?php echo erLhcoreClassDesign::baseurl('mailarchive/edit')?>/<?php echo $archive->id?>" method="post">

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Name');?></label>
        <input class="form-control form-control-sm" type="text" maxlength="50" name="name" value="<?php echo htmlspecialchars($archive->name);?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Type');?></label>
        <select name="type" class="form-control form-control-sm" <?php if ($archive->id > 0 && $archive->mails_in_archive > 0) : ?>disabled="disabled"<?php endif;?> >
            <option value="0" <?php if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archive');?></option>
            <option value="1" <?php if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_BACKUP) : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Backup');?></option>
        </select>
        <div class="text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','You can change type untill there is no chats in it.');?> <?php if ($archive->id > 0 && $archive->mails_in_archive > 0) : ?>[<?php echo $archive->mails_in_archive;?>]<?php endif;?></div>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date from');?></label>
        <input class="form-control form-control-sm type="text" name="RangeFrom" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_from_edit);?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date to');?></label>
        <input class="form-control form-control-sm" type="text" name="RangeTo" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_to_edit);?>" />
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-danger float-end" name="Delete_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>"/>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Save_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_and_continue_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save and continue');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>