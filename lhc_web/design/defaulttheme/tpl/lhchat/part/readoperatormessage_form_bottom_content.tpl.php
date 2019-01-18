<textarea class="form-control form-group <?php if ($hasExtraField !== true) : ?>btop-reset btrad-reset mb0<?php endif;?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Type your message here and hit enter to send...');?>" id="id_Question" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/readoperatormessage_after_textarea_multiinclude.tpl.php'));?>

<?php if ($hasExtraField === true) : ?>
<div class="btn-group" role="group" aria-label="...">
<input type="submit" name="askQuestionAction" id="idaskQuestionAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Send');?>" class="btn btn-default btn-sm"/>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/readoperatormessage_button_multiinclude.tpl.php'));?>
<?php if ($hasExtraField === true) : ?>
</div>
<?php endif;?>