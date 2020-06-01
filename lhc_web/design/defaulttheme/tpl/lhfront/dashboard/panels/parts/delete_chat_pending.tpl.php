<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deletechat')) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat')?>" class="material-icons float-right" ng-click="lhc.deleteChat(chat.id)">delete</a>
<?php endif; ?>
