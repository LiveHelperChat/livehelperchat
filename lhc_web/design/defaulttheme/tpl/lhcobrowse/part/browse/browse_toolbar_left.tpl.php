<div class="col-6">
    <div class="row">
        <div class="col-9">
            <input class="form-control form-control-sm" id="awesomebar" name="url" value="<?php echo htmlspecialchars($browse->url)?>" type="text">
        </div>
        <div class="col-3">
            <a href="#" class="btn btn-primary btn-sm col-12" onclick="return lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_cobrowse:<?php echo $browse->chat_id?>_<?php echo $browse->chat->hash?>:<?php echo (int)erLhcoreClassModelChatConfig::fetch('sharing_auto_allow')->current_value?>')"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Request screen share')?>" class="material-icons">visibility</i></a>
        </div>
    </div>
</div>