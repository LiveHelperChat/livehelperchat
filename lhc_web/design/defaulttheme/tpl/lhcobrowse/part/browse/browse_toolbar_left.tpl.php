<div class="col-6">
    <div class="row">
        <div class="col-9">
            <input class="form-control" id="awesomebar" name="url" value="<?php echo htmlspecialchars($browse->url)?>" type="text">
        </div>
        <div class="col-3">
            <a href="#" class="btn btn-primary btn-xs col-12" onclick="return lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_cobrowse:<?php echo $browse->chat_id?>_<?php echo $browse->chat->hash?>')"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Request screen share')?>" class="material-icons">&#xf208;</i></a>
        </div>
    </div>
</div>