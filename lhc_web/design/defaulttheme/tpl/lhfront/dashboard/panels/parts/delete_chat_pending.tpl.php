<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deletechat')) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat')?>" class="material-icons float-end" ng-click="lhc.deleteChat(chat.id);$event.stopPropagation()">delete</a>
<?php endif; ?>
