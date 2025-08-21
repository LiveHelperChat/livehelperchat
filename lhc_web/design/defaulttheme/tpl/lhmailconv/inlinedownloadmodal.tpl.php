<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Image preview');
$modalSize = 'xl';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if ($file_exists === true) : ?>
    <img class="img-fluid" src="<?php erLhcoreClassBBCode::getHost();?><?php echo erLhcoreClassDesign::baseurl('mailconv/inlinedownload') ?>/<?php echo (int)$params['id']?>/<?php echo (int)$params['id_conv']?>" alt="" title="">
<?php else : ?>
    <div class="text-center p-4">
        <div class="loading-indicator">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Loading...');?></span>
            </div>
            <div class="mt-2">
                <small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Downloading image...');?></small>
            </div>
        </div>
        <img class="img-fluid d-none" id="preview-image" src="<?php erLhcoreClassBBCode::getHost();?><?php echo erLhcoreClassDesign::baseurl('mailconv/inlinedownload') ?>/<?php echo (int)$params['id']?>/<?php echo (int)$params['id_conv']?>" alt="" title="" onload="this.classList.remove('d-none'); this.parentElement.querySelector('.loading-indicator').style.display='none';">
    </div>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>