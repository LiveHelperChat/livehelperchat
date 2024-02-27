<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','New archive');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($step_2)) : ?>

    <?php if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhmailarchive/process_content.tpl.php'));?>
    <?php else : ?>
        <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','You are ready to backup your e-mails.');?></a>
    <?php endif; ?>

<?php else : ?>
    <form ng-non-bindable action="<?php echo erLhcoreClassDesign::baseurl('mailarchive/newarchive')?>" method="post">

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Name');?></label>
            <input class="form-control form-control-sm" type="text" maxlength="50" name="name" value="<?php echo htmlspecialchars($archive->name);?>" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Type');?></label>
            <select id="archive-type" name="type" class="form-control form-control-sm">
                <option value="0" <?php if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Backup');?></option>
                <option value="1" <?php if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_BACKUP) : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archive');?></option>
            </select>
            <div class="text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','You can change type until there is no mails in it.');?></div>
        </div>

        <div class="row" id="date-range-depend">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date from');?></label>
                    <input class="form-control form-control-sm" type="text" name="RangeFrom" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_from_edit);?>" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date to');?></label>
                    <input class="form-control form-control-sm" type="text" name="RangeTo" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_to_edit);?>" />
                </div>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <div class="btn-group btn-group-sm" role="group" aria-label="...">
            <input type="submit" class="btn btn-secondary" name="Save_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Continue');?>"/>
            <input type="submit" class="btn btn-secondary" name="Cancel_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
        </div>

    </form>

    <script>
        $('#archive-type').change(function(){
            if ($(this).val() == '1') {
                $('#date-range-depend').hide();
            } else {
                $('#date-range-depend').show();
            }
        });
        $('#archive-type').val() == '1' ? $('#date-range-depend').hide() : $('#date-range-depend').show();
    </script>

<?php endif;?>