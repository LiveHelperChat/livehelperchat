<?php if ($currentUser->hasAccessTo('lhmailconv','use_admin')) : ?>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats and Mails')?>
<?php else : ?>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats')?>
<?php endif; ?>
