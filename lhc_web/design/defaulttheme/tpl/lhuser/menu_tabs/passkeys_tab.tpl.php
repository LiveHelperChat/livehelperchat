<?php if (erLhcoreClassUserPasskey::isEnabled() && erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'use_passkey')) : ?>
<li role="presentation" class="nav-item"><a href="#passkeys" class="nav-link <?php if ($tab == 'tab_passkeys') : ?>active<?php endif;?>" aria-controls="passkeys" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Passkeys');?></a></li>
<?php endif; ?>
