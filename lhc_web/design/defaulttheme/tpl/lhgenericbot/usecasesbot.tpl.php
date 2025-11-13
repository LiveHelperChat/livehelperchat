<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Use cases');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php if ($bot !== null) : ?>
                   
                <?php if (!empty($items)) : ?>
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','This Bot is used in the following places');?>:</p>
                    <?php foreach ($items as $item) : ?>
                        <div class="lhc-item-list mb-2">
                            <div class="lhc-item-list-title fs13">
                               <?php if ($item['type'] == 'webhook') : ?>
                                   <i class="material-icons">webhook</i><a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/edit')?>/<?php echo $item['item']->id?>" target="_blank"><?php echo htmlspecialchars(($item['item']->name ?: 'Webhook #' . $item['item']->id) . ($item['item']->event ? ' | ' . $item['item']->event : ''))?></a>
                                   <span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Webhook');?></span>
                                   <?php if ($item['item']->type == 1) : ?><span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Continuous Chat');?></span><?php endif; ?>
                                   <?php if ($item['item']->type == 2) : ?><span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Continuous Mail');?></span><?php endif; ?>
                               <?php elseif ($item['type'] == 'auto_responder') : ?>
                                   <i class="material-icons">support_agent</i><a class="me-1" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit/AutoResponder')?>/<?php echo $item['item']->id?>" target="_blank"><?php echo htmlspecialchars($item['item']->name)?></a>
                                   <span class="badge bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Auto Responder');?></span>
                               <?php elseif ($item['type'] == 'widget_theme') : ?>
                                   <i class="material-icons">widgets</i><a class="me-1" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit/WidgetTheme')?>/<?php echo $item['item']->id?>" target="_blank"><?php echo htmlspecialchars($item['item']->name)?></a>
                                   <span class="badge bg-primary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Widget Theme');?></span>
                               <?php elseif ($item['type'] == 'proactive_invitation') : ?>
                                   <i class="material-icons ">notifications_active</i> <a class="me-1" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit/ProactiveChatInvitation')?>/<?php echo $item['item']->id?>" target="_blank"><?php echo htmlspecialchars($item['item']->name)?></a>
                                   <span class="badge bg-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Proactive Invitation');?></span>
                               <?php elseif ($item['type'] == 'bot_command') : ?>
                                   <i class="material-icons">terminal</i><a class="me-1" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editcommand')?>/<?php echo $item['item']->id?>" target="_blank"><?php echo htmlspecialchars(($item['item']->name ?: 'Command #' . $item['item']->id) . ' | ' . $item['item']->command)?></a>
                                   <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Bot Command');?></span>
                               <?php elseif ($item['type'] == 'department') : ?>
                                   <i class="material-icons">home</i><a class="me-1" href="<?php echo erLhcoreClassDesign::baseurl('departament/edit')?>/<?php echo $item['item']->id?>" target="_blank"><?php echo htmlspecialchars($item['item']->name)?></a>
                                   <span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Department');?></span>
                               <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="alert alert-info">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','This Bot is not used anywhere yet.');?>
                    </div>
                <?php endif; ?>
                
            <?php else : ?>
                <div class="alert alert-danger">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Bot not found.');?>
                </div>
            <?php endif; ?>
        </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>