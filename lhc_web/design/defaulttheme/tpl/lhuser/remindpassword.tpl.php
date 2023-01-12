<?php if (isset($hash)) : ?>

    <?php if (isset($account_updated)) : ?>

        <?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Password was reset. Please login now.');$hideSuccessButton = true; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>

    <?php elseif (isset($manual_password) && $manual_password == 1) : ?>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','New password was set, copy it or');?>&nbsp;<a href="#" class="action-image" onclick="$('#new-password').get(0).type = 'text';return false;"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','click to show');?>.</a></label>
            <div class="input-group input-group-sm mb-3">
                <input readonly class="form-control" type="password" id="new-password" value="<?php echo htmlspecialchars($new_password)?>" />
                <span class="input-group-text">
                    <span data-success="Copied" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Copy');?>" data-copy="<?php echo htmlspecialchars($new_password)?>" onclick="lhinst.copyContent($(this))" class="material-icons me-0 action-image">content_copy</span>
                </span>
            </div>
        </div>

        <a class="btn btn-primary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('user/login')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Login')?></a>

    <?php else : ?>
        <form action="<?php echo erLhcoreClassDesign::baseurl('user/remindpassword')?>/<?php echo htmlspecialchars($hash)?>" method="post" autocomplete="off" ng-non-bindable>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <?php if (isset($errors)) : $hideErrorButton = true;?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php endif; ?>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="col-form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','New password')?>*</label>
                        <input type="password" required name="Password1" autocomplete="off" class="form-control form-control-sm" />
                    </div>
                    <div class="form-group">
                        <label class="col-form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Repeat password')?>*</label>
                        <input type="password" required name="Password2" autocomplete="off" class="form-control form-control-sm" />
                    </div>
                </div>
                <div class="col-6">
                    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Password requirements')?></h6>
                    <ul class="text-muted fs13 ps-3">
                        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Both passwords must match');?></li>
                        <?php if (isset($minimum_length)) : ?>
                            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Minimal password length');?> - <?php echo htmlspecialchars($minimum_length)?></li>
                        <?php endif; ?>
                        <?php if (isset($lowercase_required)) : ?>
                            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Lowercase characters required');?> - <?php echo htmlspecialchars($lowercase_required)?></li>
                        <?php endif; ?>
                        <?php if (isset($uppercase_required)) : ?>
                            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Uppercase letters required');?> - <?php echo htmlspecialchars($uppercase_required)?></li>
                        <?php endif; ?>
                        <?php if (isset($number_required)) : ?>
                            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Numbers required');?> - <?php echo htmlspecialchars($number_required)?></li>
                        <?php endif; ?>
                        <?php if (isset($special_required)) : ?>
                            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Special characters required');?> - <?php echo htmlspecialchars($special_required)?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Reset password')?></button>

            <a class="btn btn-outline-secondary float-end btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('user/login')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Login')?></a>
        </form>
    <?php endif; ?>

<?php else : $errors = [erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Invalid hash or it was used already!')];$hideErrorButton = true;?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>

    <a class="btn btn-primary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('user/login')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/remindpassword','Login')?></a>

<?php endif; ?>