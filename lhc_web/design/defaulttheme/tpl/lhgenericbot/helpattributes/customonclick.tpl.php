<?php if ($context == 'customonclick') : ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'You can have custom JS execution on click event. In combination with');?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Hide content on click');?></span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'you can have your own invitation workflow.');?></p>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Some examples of JS');?></p>
    <div class="alert alert-success" role="alert">
        window.parent.document.location = 'https://example.com/go_to_page.html';
    </div>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Call page function where widget is embedded');?></p>
    <div class="alert alert-success" role="alert">
        window.parent.parentPageFunction();
    </div>
<?php endif; ?>