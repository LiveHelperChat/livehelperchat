<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block visitor')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div id="block-bloc-<?php echo $chat->id?>"></div>
<form id="block-form">
    <div class="form-group">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/blockuser/blockoptions.tpl.php'));?>
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Expires')?></label>
        <select class="form-control form-control-sm" name="expires" id="block-expires">
            <option value="3">3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days (cool off)')?></option>
            <option value="15">15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="30">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="60">60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="90">90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="120">120 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="240">240 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="360">360 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','days')?></option>
            <option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Permanent/unlimited')?></option>
        </select>
    </div>
    <button type="button" onclick="blockByChat()" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block')?></button>
</form>
<script>
    function blockByChat(){
        $.postJSON(WWW_DIR_JAVASCRIPT + 'chat/blockuser/<?php echo $chat->id?>', $('#block-form').serialize(), function(data){
            $('#block-bloc-<?php echo $chat->id?>').html(data.result);
            if (!data.error) {
                setTimeout(function (){
                    $('#myModal').modal('hide');
                },2000);
            }
        });
    }
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>