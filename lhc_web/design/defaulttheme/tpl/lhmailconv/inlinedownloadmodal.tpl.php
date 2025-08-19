<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Image preview');
$modalSize = 'xl';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<img class="img-fluid" src="<?php erLhcoreClassBBCode::getHost();?><?php echo erLhcoreClassDesign::baseurl('mailconv/inlinedownload') ?>/<?php echo (int)$params['id']?>/<?php echo (int)$params['id_conv']?>" alt="" title="">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>