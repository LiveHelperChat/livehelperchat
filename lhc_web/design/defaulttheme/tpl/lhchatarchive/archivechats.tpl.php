<?php if ($status['pending_archive'] == 'false') : ?>
<div class="alert-box secondary">
          <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archivechats','Archiving has finished.');?>

          <a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/listarchivechats')?>/<?php echo $archive->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archivechats','See archived chats');?></a>

</div>
<?php endif; ?>
<div>
<div class="radius secondary label"><?php echo date(erLhcoreClassModule::$dateDateHourFormat)?>, FCID - <?php echo $status['fcid']?>, LCID - <?php echo $status['lcid']?>, AC - <?php echo $status['chats_archived']?>, AM - <?php echo $status['messages_archived']?></div>
</div>