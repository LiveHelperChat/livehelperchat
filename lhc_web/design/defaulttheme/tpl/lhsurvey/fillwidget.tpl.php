<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/parts/header.tpl.php'));?>

<form action="" method="post">
    <?php if (isset($errors)) : ?>
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fill.tpl.php'));?>
    
    <hr class="mt10 mb10">
    
    
    <div class="btn-group" role="group" aria-label="...">
        <?php if ($survey_item->is_filled == false) : ?>
            <input type="submit" class="btn btn-success btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?>" name="Vote" />
        <?php endif;?>
        <a class="btn btn-info btn-sm" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/chatpreview/<?php echo $chat->id?>/<?php echo $chat->hash?>'})"><i class="material-icons">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Preview chat')?></a>
    </div>
    
    <?php if ($survey_item->is_filled == true) : ?>
         <input type="button" class="btn btn-success mb10 pull-right" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
    <?php endif;?>
    
</form>


<script type="text/javascript">
lhinst.setChatID('<?php echo $chat->id?>');
lhinst.setChatHash('<?php echo $chat->hash?>');
</script>