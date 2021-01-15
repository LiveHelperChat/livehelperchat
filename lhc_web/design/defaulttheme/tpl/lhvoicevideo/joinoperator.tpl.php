<div id="root" class="container-fluid d-flex flex-column flex-grow-1 overflow-auto">
    <div class="d-flex flex-row flex-grow-1 pt-2">
        <div class="col bg-light mx-1 align-middle text-center d-flex">
            <div class="align-self-center mx-auto ">Op 1</div>
        </div>
        <div class="col bg-light mx-1 align-middle text-center d-flex">
            <div class="align-self-center mx-auto">Op 2</div>
        </div>
        <div class="col bg-light mx-1 align-middle text-center d-flex">
            <div class="align-self-center mx-auto">Op 2</div>
        </div>
    </div>
    <div class="row header-chat desktop-header">
        <div class="p-2 text-center mx-auto">
            <button class="btn btn-secondary">Join with audio</button>
            <button class="btn btn-secondary">Join with audio & video</button>
        </div>
    </div>
</div>
<script>
    var WWW_DIR_LHC_WEBPACK_ADMIN = '<?php echo erLhcoreClassDesign::design('js/voice_op')?>/';
    (function (){
        var initParams = <?php
            $params = array (
                'id' => $chat->id,
                'hash' => $chat->hash,
                'appid' => $voice_data['agora_app_id'],
            );
            echo json_encode($params); ?>;
        window.initParams = initParams;

    })();
</script>
<script src="<?php echo erLhcoreClassDesign::designJS('js/voice_op/voice.call.js');?>"></script>