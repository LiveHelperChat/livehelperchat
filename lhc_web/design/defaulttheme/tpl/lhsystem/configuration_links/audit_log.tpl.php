<?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>
    <?php if ($currentUser->hasAccessTo('lhsystem','auditlog')) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Audit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Audit Logs');?></a></li>
        <?php if ($currentUser->hasAccessTo('lhaudit','configuration')) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('audit/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Configuration');?></a></li>
        <?php endif; ?>
    <?php endif; ?>
<?php endif;?>