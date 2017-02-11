<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Collected information')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div class="row">    
    <div class="columns col-md-12"> 
        <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>
        <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/collecteditem_display.tpl.php'));?>
    </div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>