<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhcobrowse', 'browse')) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screen sharing')?>" class="icon-eye" href="#" onclick="return lhinst.startCoBrowse('<?php echo $chat->id?>')"></a>
<?php endif;?>