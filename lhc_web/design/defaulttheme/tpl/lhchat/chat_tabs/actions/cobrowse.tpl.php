<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/cobrowse_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_cobrowse_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhcobrowse', 'browse')) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screen sharing')?>" class="icon-eye" href="#" onclick="return lhinst.startCoBrowse('<?php echo $chat->id?>')"></a>
<?php endif;?>