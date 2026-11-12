<?php if (erLhcoreClassUserPasskey::isEnabled() && erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'use_passkey')) : ?>
<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_passkeys') : ?>active<?php endif;?>" id="passkeys">

    <div class="explain mb-3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Passkeys let you login without a password by using your fingerprint, face, a PIN or a hardware security key.');?></div>

    <div data-lh-passkey-container data-url-register-options="<?php echo erLhcoreClassDesign::baseurl('user/passkey')?>/register_options" data-url-reauth-options="<?php echo erLhcoreClassDesign::baseurl('user/passkey')?>/reauth_options" data-url-reauth="<?php echo erLhcoreClassDesign::baseurl('user/passkey')?>/reauth" data-has-passkeys="<?php echo !empty($passkeys) ? '1' : '0'?>" data-url-register="<?php echo erLhcoreClassDesign::baseurl('user/passkey')?>/register" data-url-delete="<?php echo erLhcoreClassDesign::baseurl('user/passkey')?>/delete">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Name');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Added');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Last used');?></th>
                    <th class="text-end">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($passkeys)) : ?>
                <tr>
                    <td colspan="4" class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','You do not have any passkeys yet');?></td>
                </tr>
                <?php else : ?>
                    <?php foreach ($passkeys as $passkey) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($passkey->name)?></td>
                        <td><?php echo htmlspecialchars($passkey->created_at_front)?></td>
                        <td><?php echo $passkey->last_used_at_front != '' ? htmlspecialchars($passkey->last_used_at_front) : erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Never');?></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-xs btn-outline-danger" data-lh-passkey-delete="<?php echo (int)$passkey->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Delete');?></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($user->passwordless_login != 1) : ?>
        <div class="form-group mb-2">
            <label for="passkey-current-password"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Current password');?></label>
            <input id="passkey-current-password" type="password" class="form-control form-control-sm" autocomplete="current-password" data-lh-passkey-password />
        </div>
        <?php else : ?>
        <div class="alert alert-info mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Passwordless login is enabled for this account, so the current password is not accepted. A new passkey has to be confirmed with one of your existing passkeys, and the last passkey can not be deleted while passwordless login is enabled.');?></div>
        <?php endif; ?>
        <div class="input-group input-group-sm col-6 ps-0">
            <input type="text" class="form-control" maxlength="100" data-lh-passkey-name placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Passkey name, e.g. Work laptop');?>" />
            <button class="btn btn-secondary" type="button" data-lh-passkey-add><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Add a passkey');?></button>
        </div>

        <div class="text-danger small mt-2" data-lh-passkey-error></div>
    </div>

</div>
<?php endif; ?>
