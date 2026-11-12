<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Passkey options')?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <label>
            <input type="checkbox" value="on" name="enabled" <?php echo isset($data['enabled']) && $data['enabled'] == 1 ? 'checked="checked"' : '' ?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Enable passkeys (passwordless login)')?>
        </label>
        <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','While disabled passkeys can not be registered and the login page does not offer passkey login.')?></div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Relying party ID')?></label>
                <input class="form-control form-control-sm" type="text" name="rp_id" value="<?php echo htmlspecialchars(isset($data['rp_id']) ? $data['rp_id'] : '')?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Current host name')?>" />
                <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Domain name used to identify this site. Leave empty to use the current host name')?>: <strong><?php echo htmlspecialchars($detected_host)?></strong>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Changing it invalidates already registered passkeys.')?></div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Relying party name')?></label>
                <input class="form-control form-control-sm" type="text" name="rp_name" value="<?php echo htmlspecialchars(isset($data['rp_name']) ? $data['rp_name'] : '')?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Current host name')?>" />
                <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Name shown to the user by the browser or the operating system. Defaults to the current host name')?>: <strong><?php echo htmlspecialchars($detected_host)?></strong></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Relying party IDs allowed over HTTP')?></label>
        <input class="form-control form-control-sm" type="text" name="secured_rp_ids" value="<?php echo htmlspecialchars(isset($data['secured_rp_ids']) ? $data['secured_rp_ids'] : '')?>" placeholder="localhost" />
        <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Comma separated list of relying party IDs (host names) which are allowed to use passkeys without HTTPS, for example example.com. No https:// and no port.')?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Localhost is always allowed, HTTPS origins never have to be listed. Current origin')?>: <strong><?php echo htmlspecialchars($current_origin)?></strong></div>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Allowed origins')?></label>
        <input class="form-control form-control-sm" type="text" name="allowed_origins" value="<?php echo htmlspecialchars(isset($data['allowed_origins']) ? $data['allowed_origins'] : '')?>" placeholder="https://example.com" />
        <div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Required when passkeys are enabled. Enter comma separated exact origins including scheme and optional port, for example https://example.com:8443. Subdomains must be listed separately.')?></div>
    </div>

    <input type="submit" class="btn btn-secondary btn-sm" name="StorePasskeyOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Save')?>" />

</form>

<hr />

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Registered passkeys')?></h5>

<?php if (empty($passkeys)) : ?>
    <div class="text-muted small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','There are no registered passkeys yet.')?></div>
<?php else : ?>
    <table class="table table-sm table-hover" ng-non-bindable>
        <thead>
            <tr>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','User')?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Name')?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Type')?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Added')?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Last used')?></th>
                <th class="text-end" width="1%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($passkeys as $passkey) : ?>
            <?php $passkeyUser = isset($user_map[$passkey->user_id]) ? $user_map[$passkey->user_id] : false; ?>
            <tr>
                <td>
                    <?php if ($passkeyUser instanceof erLhcoreClassModelUser) : ?>
                        <a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo (int)$passkeyUser->id?>" target="_blank"><?php echo htmlspecialchars($passkeyUser->username)?></a>
                        <?php if ($passkeyUser->passwordless_login == 1) : ?>
                            <span class="material-icons text-warning" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Passwordless login is enabled for this user')?>">lock</span>
                        <?php endif;?>
                    <?php else : ?>
                        <?php echo (int)$passkey->user_id?>
                    <?php endif;?>
                </td>
                <td><?php echo htmlspecialchars($passkey->name)?></td>
                <td title="<?php echo htmlspecialchars($passkey->aaguid)?>"><?php echo htmlspecialchars($passkey->type_front)?></td>
                <td><?php echo htmlspecialchars($passkey->created_at_front)?></td>
                <td><?php echo $passkey->last_used_at_front != '' ? htmlspecialchars($passkey->last_used_at_front) : erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Never')?></td>
                <td nowrap="">
                    <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('user/passkeys')?>/(action)/delete/(id)/<?php echo (int)$passkey->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey','Delete')?></a>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif;?>
