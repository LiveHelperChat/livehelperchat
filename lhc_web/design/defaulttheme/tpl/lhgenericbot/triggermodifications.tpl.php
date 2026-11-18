<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons fs18 align-middle">history</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Trigger modification history');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php if ($bot !== null) : ?>
                <?php if (!empty($triggers)) : ?>
                    <p class="fs12 text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Latest 10 trigger modifications for this bot');?></p>
                    <table class="table table-sm table-hover">
                        <thead>
                        <tr>
                            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','ID');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Trigger');?></th>
                            <th width="22%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Modified');?></th>
                            <th width="28%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Modified by');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($triggers as $trigger) : ?>
                            <tr>
                                <td><?php echo (int)$trigger->id?></td>
                                <td>
                                    <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/bot')?>/<?php echo (int)$bot->id?>#!#/trigger-<?php echo (int)$trigger->id?>"><?php echo htmlspecialchars($trigger->name !== '' ? $trigger->name : '#' . $trigger->id)?></a>
                                </td>
                                <td title="<?php echo date('Y-m-d H:i:s', (int)$trigger->updated_at)?>">
                                    <?php echo erLhcoreClassChat::formatSeconds(max(0, time() - (int)$trigger->updated_at))?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo','ago');?>
                                </td>
                                <td>
                                    <?php if (isset($users[$trigger->muser_id])) : ?>
                                        <?php echo htmlspecialchars($users[$trigger->muser_id])?>
                                    <?php else : ?>
                                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo','Unknown');?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-info">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','There are no trigger modifications recorded for this bot yet.');?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="alert alert-danger">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bot not found.');?>
                </div>
            <?php endif; ?>
        </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
