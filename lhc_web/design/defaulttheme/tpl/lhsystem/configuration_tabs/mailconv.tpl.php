<?php if ($currentUser->hasAccessTo('lhmailconv','use_admin')) : ?>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#mailconv" aria-controls="mailconv" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Mail conversation');?></a></li>
<?php endif;?>