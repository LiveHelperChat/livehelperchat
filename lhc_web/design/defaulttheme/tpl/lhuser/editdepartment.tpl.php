<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','Edit department assignment'); ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <?php if ($userDep instanceof erLhcoreClassModelUserDep || $userDep instanceof erLhcoreClassModelDepartamentGroupUser) : ?>

    <?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
        <script>$.get('<?php echo erLhcoreClassDesign::baseurl('user/userdepartments')?>/<?php echo $user->id?>',function(data){$('#departments').html(data);})</script>
    <?php endif; ?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>

    <form action="<?php echo erLhcoreClassDesign::baseurl('user/editdepartment')?>/<?php echo $user->id?>/<?php echo $dep->id?><?php $userDep instanceof erLhcoreClassModelDepartamentGroupUser ? print '/(mode)/group' : ''?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
        <?php include(erLhcoreClassDesign::designtpl('lhuser/department/attributes.tpl.php'));?>
        <input type="submit" class="btn btn-sm btn-secondary" name="update" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">
    </form>

    <?php else : ?>
        <?php $errors = [erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Department assignment could not be found!')]; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>