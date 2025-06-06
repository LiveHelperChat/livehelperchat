<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Use cases');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php foreach ($items as $item) : ?>
                <div class="lhc-item-list">
                    <div class="lhc-item-list-title">
                        <?php if ($item['type'] == 'translation') : ?>
                            <i class="material-icons">translate</i> [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Individualization item');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edittritem') . '/' . $item['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelGenericBotTrItem::fetch($item['id'])->identifier); ?></a>
                        <?php elseif ($item['type'] == 'trigger') : ?>
                            <i class="material-icons">action_key</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Trigger');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/bot')?>/<?php echo erLhcoreClassModelGenericBotTrigger::fetch($item['id'])->bot_id?>#!#/trigger-<?php echo $item['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelGenericBotTrigger::fetch($item['id'])->name)?></a>
                        <?php elseif ($item['type'] == 'priority') : ?>
                            <i class="material-icons">label</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat priority');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit');?>/ChatPriority/<?php echo $item['id'];?>" class="text-decoration-none"><?php echo $item['id'];?></a>
                        <?php elseif ($item['type'] == 'replace') : ?> 
                            <i class="material-icons">swap_horiz</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Replaceable variable ');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/editreplace') . '/' . $item['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelCannedMsgReplace::fetch($item['id'])->identifier); ?></a>
                        <?php elseif ($item['type'] == 'cannedmsg') : ?>
                            <i class="material-icons">message</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Canned message');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsgedit')?>/<?php echo $item['id']?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelCannedMsg::fetch($item['id'])->title)?></a>
                        <?php elseif ($item['type'] == 'bot_condition') : ?>
                            <i class="material-icons">condition</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Condition');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editcondition')?>/<?php echo $item['id']?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelGenericBotCondition::fetch($item['id'])->name)?></a>
                        <?php elseif ($item['type'] == 'mail_template') : ?>
                            <i class="material-icons">email</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Mail response template');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editresponsetemplate')?>/<?php echo $item['id']?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelMailconvResponseTemplate::fetch($item['id'])->name)?></a>
                        <?php elseif ($item['type'] == 'proactive_invitation') : ?>
                            <i class="material-icons">chat</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Proactive invitation');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit')?>/ProactiveChatInvitation/<?php echo $item['id']?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhAbstractModelProactiveChatInvitation::fetch($item['id'])->name)?></a>
                        <?php elseif ($item['type'] == 'auto_responder') : ?>
                            <i class="material-icons">auto_awesome</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Auto responder');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit')?>/AutoResponder/<?php echo $item['id']?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhAbstractModelAutoResponder::fetch($item['id'])->name)?></a>
                        <?php elseif ($item['type'] == 'webhook') : ?>
                            <i class="material-icons">webhook</i>  [<?php echo  $item['id']?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Webhook');?> - <a targer="_blank" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/edit')?>/<?php echo $item['id']?>" class="text-decoration-none"><?php echo htmlspecialchars(erLhcoreClassModelChatWebhook::fetch($item['id'])->name . ' ' . erLhcoreClassModelChatWebhook::fetch($item['id'])->event)?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>