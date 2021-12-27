<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">
    <input type="hidden" name="doSearch" value="1">
    
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-secondary btn-sm"  onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/newcampaignrecipient')?>/<?php echo $campaign->id?>'})">
            <i class="material-icons">add</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New manual recipient');?>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/importfrommailinglist')?>/<?php echo $campaign->id?>'})" >
            <i class="material-icons">playlist_add</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update recipients from mailing list');?>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'mailing/importcampaign/<?php echo $campaign->id?>'})" >
            <i class="material-icons">file_upload</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Import');?>
        </button>
    </div>

    <div role="alert" class="alert alert-info alert-dismissible hide m-3" id="list-update-import">
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','This list was updated. Please');?>&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $campaign->id?>?refresh=<?php echo time()?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','refresh');?>.</a>
    </div>

</form>




