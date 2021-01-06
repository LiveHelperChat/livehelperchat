<?php if (isset($orderInformation['id']['enabled']) && $orderInformation['id']['enabled'] == true) : ?>
<div class="col-6 pb-1">
    <span class="material-icons">vpn_key</span><?php echo $chat->id;?> <button data-success="Copied" class="btn btn-xs btn-link text-muted py-1" data-copy="<?php echo (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] ?><?php echo erLhcoreClassDesign::baseurl('front/default')?>/#!#chat-id-<?php echo $chat->id?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons">link</i>Copy link</button>
</div>
<?php endif; ?>