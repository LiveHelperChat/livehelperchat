<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process','Process archive');?></h1>

<?php if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhmailarchive/process_content.tpl.php'));?>
<?php else : ?>
    <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','You are ready to backup your e-mails.');?></a>
<?php endif; ?>