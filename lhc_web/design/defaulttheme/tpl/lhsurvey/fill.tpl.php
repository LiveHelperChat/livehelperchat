<div class="fill-survey-container">
    <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/parts/header.tpl.php'));?>
    <div class="fill-survey-form">
        <form action="" method="post">
            <?php if (isset($errors)) : ?>
            		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php endif; ?>
            
            <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fill.tpl.php'));?>
            
            <div class="btn-group" role="group" aria-label="...">
                <?php if ($survey_item->is_filled == false) : ?>
                    <input type="submit" class="btn btn-success btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?>" name="Vote" />
                <?php endif;?>
                    <a class="btn btn-info btn-sm" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/chatpreview/<?php echo $chat->id?>/<?php echo $chat->hash?>'})"><i class="material-icons">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Preview chat')?></a>
             </div>
        </form>
    </div>
</div>