<?php if ($context == 'timefilter') : ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'To choose yesterday.');?></p>
    <ul>
        <li>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'As start day choose yesterday day');?><br>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Hour and minute from');?> <span class="badge bg-secondary">00 h.</span> <span class="badge bg-secondary">00 m.</span> <span class="badge bg-secondary">00 s.</span><br>
            <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Date range to');?></span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'choose today and choose');?>
            <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Hour and minute to');?></span> <span class="badge bg-secondary">23 h.</span> <span class="badge bg-secondary">59 m.</span> <span class="badge bg-secondary">59 s.</span>
        </li>
    </ul>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Date from and to are always inclusive.');?></p>
<?php endif; ?>