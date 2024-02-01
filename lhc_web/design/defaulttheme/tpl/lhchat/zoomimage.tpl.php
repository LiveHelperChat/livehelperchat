<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Image preview');
$modalSize = 'xl';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <?php if (isset($externalImage) && $externalImage == true) : ?>
        <img class="img-fluid" src="<?php echo erLhcoreClassBBCode::esc_url($fileImage)?>" alt="" title="">
    <?php else : ?>
        <img class="img-fluid" src="<?php erLhcoreClassBBCode::getHost();?><?php echo erLhcoreClassDesign::baseurl('file/downloadfile') ?>/<?php echo $fileImage->id?>/<?php echo $fileImage->security_hash?>/(inline)/true" alt="" title="">
    <?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>