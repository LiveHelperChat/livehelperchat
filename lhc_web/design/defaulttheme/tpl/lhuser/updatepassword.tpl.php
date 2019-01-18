<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Your password has expired. Please update it');?></h4>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($account_updated) && $account_updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Password was updated. Now you can go to back office.'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <p>
        <a class="btn btn-primary" href="<?php echo erLhcoreClassDesign::baseurl('/')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Go to back office');?></a>
    </p>
<?php else : ?>
    <form method="post" autocomplete="off" action="<?php echo erLhcoreClassDesign::baseurl('user/updatepassword')?>/<?php echo $userId?>/<?php echo $ts?>/<?php echo $hash?>">

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Old password');?></label>
            <input class="form-control" autocomplete="new-password" type="text" name="OldPassword" value="" />
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','New password');?></label>
                    <input type="password" autocomplete="new-password" class="form-control" name="NewPassword" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Repeat password');?></label>
                    <input type="password" autocomplete="new-password" class="form-control" name="NewPassword1" value="" />
                </div>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <input type="submit" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Update password');?>" name="UpdatePassword" />

    </form>
<?php endif; ?>
