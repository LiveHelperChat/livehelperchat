<h4>
 <?php if ($theme !== false && $theme->support_closed != '')  : ?>
   <?php echo htmlspecialchars($theme->support_closed) ?>
<?php  else  : ?>
   <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support staff member has closed this chat')?>
<?php endif;?>
</h4>

<?php if ($modeembed == 'widget') : ?>
<input type="button" class="btn btn-default btn-sm mb10" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
<?php endif;?>
