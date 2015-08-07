<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/parts/header.tpl.php'));?>

<form action="" method="post">
    <?php if (isset($errors)) : ?>
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fill.tpl.php'));?>
    
    <?php if ($survey_item->is_filled == false) : ?>
        <input type="submit" class="btn btn-default btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?>" name="Vote" />
    <?php else : ?>
        <?php $timeout = 1500; 
        if (isset($just_stored) && $just_stored == true) : $timeout = 3000;?>
            <input type="button" class="btn btn-default mb10" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
        <?php endif; ?>
        <script>
        setTimeout(function() {
        	lhinst.userclosedchatembed();
        },<?php echo $timeout?>); 
        </script>
    <?php endif;?>
</form>