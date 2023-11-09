<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><?php if (isset($Result['modal_header_title'])) : ?><?php echo $Result['modal_header_title']?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?><?php endif; ?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body mx550">
            <?php echo $Result['content']?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>

