<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copy messages')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Messages')?></label>
    <textarea id="chat-copy-messages" rows="10" class="form-control"><?php echo htmlspecialchars($messages)?></textarea>
</div>

<a class="btn btn-info" data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copied!')?>" onclick="lhinst.copyMessages($(this))"><i class="material-icons">&#xE14D;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copy to clipboard')?></a>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>