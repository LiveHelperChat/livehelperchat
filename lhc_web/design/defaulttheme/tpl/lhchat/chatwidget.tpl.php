<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?>">

<div class="row">
    <div class="columns six mobile-two">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?></label>
        <input type="text" class="inputfield" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
    </div>
    <div class="columns six mobile-two">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?></label>
        <input type="text" class="inputfield" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
    </div>
</div>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?></label>
<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>

<input type="submit" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChat" />
<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>

</form>