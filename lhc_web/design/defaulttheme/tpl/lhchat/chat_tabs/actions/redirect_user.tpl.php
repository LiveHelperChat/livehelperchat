<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowredirect')) : ?>
<div class="col-6 pb-1">
<a class="text-muted" onclick="lhinst.redirectToURL('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Please enter a URL');?>')">
    <span class="material-icons">link</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect to another url');?>
</a>
</div>
<?php endif;?>