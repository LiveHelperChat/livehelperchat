<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">
    <input type="hidden" name="doSearch" value="1">
    
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-secondary btn-sm"  onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/newcampaignrecipient')?>/<?php echo $campaign->id?>'})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New manual recipient');?></button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/importfrommailinglist')?>/<?php echo $campaign->id?>'})" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update recipients from mailing list');?></button>
    </div>

    <div role="alert" class="alert alert-info alert-dismissible hide m-3" id="list-update-import">
        This list was updated. Please <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $campaign->id?>?refresh=<?php echo time()?>">refresh.</a>
    </div>

</form>




