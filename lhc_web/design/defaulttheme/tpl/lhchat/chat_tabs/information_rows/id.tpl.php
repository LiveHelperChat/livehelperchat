<?php if (isset($orderInformation['id']['enabled']) && $orderInformation['id']['enabled'] == true) : ?>
<div class="col-6 pb-1">
    <span class="material-icons user-select-none">vpn_key</span><span><?php echo $chat->id;?></span><button data-success="Copied" class="ml-1 btn btn-xs btn-link text-muted py-1" data-copy="<?php echo (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] ?><?php echo erLhcoreClassDesign::baseurl('front/default')?>/(cid)/<?php echo $chat->id?>/#!#chat-id-<?php echo $chat->id?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons">link</i>Copy link</button>
</div>
<?php endif; ?>