<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','redirectcontact')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" onclick="lhinst.redirectContact('<?php echo $chat->id;?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?');?>')" ><span class="material-icons">reply</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect to contact form');?></a>
</div>
<?php endif; ?>