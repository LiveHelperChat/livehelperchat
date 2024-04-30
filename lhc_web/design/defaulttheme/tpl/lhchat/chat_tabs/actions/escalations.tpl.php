<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchatcommand','use')) : ?>
<?php $escalationCommands = erLhcoreClassModelGenericBotCommand::getList(['customfilter' => ['(dep_id = 0 OR dep_id = ' . (int)$chat->dep_id . ')'], 'filter' => ['enabled_display' => 1]]); ?>
<?php if (!empty($escalationCommands)): ?>
<div class="col-6 pb-1 ps-1-8" id="escalation-<?php echo $chat->id?>">
    <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle text-muted p-1 pt-0 pb-0 ps-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="material-icons">keyboard_command_key</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Bot commands')?>
        </button>
        <ul class="dropdown-menu pt-1 pb-1 fs13">
            <?php foreach ($escalationCommands as $escalationCommand): ?>
                <li><a class="dropdown-item text-muted action-icon pt-1 pb-1" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chatcommand/command/<?php echo $chat->id?>/<?php echo $escalationCommand->id?>'})" title="!<?php echo htmlspecialchars($escalationCommand->command)?>" ><?php echo htmlspecialchars($escalationCommand->name);?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif;endif; ?>