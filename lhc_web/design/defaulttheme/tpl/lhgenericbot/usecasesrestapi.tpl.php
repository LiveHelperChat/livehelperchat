<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Use cases');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php if ($rest_api !== null) : ?>
                   
                <?php if (!empty($items)) : ?>
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','This REST API is used in the following triggers');?>:</p>
                    
                    <?php foreach ($items as $item) : ?>
                        <div class="lhc-item-list mb-2">
                            <div class="lhc-item-list-title">
                                <i class="material-icons">&#xE8E7;</i>
                                <a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/bot')?>/<?php echo $item['bot_id']?>#!#<?php echo $item['id']?>" target="_blank">
                                    <?php echo htmlspecialchars($item['name'])?>
                                </a>
                                <?php if (!empty($item['methods'])) : ?>
                                    <span class="text-muted small"> - <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Methods used');?>:</strong> <?php echo htmlspecialchars(implode(', ', $item['methods']))?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                <?php else : ?>
                    <div class="alert alert-info">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','This REST API is not used in any triggers yet.');?>
                    </div>
                <?php endif; ?>
                
            <?php else : ?>
                <div class="alert alert-danger">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','REST API not found.');?>
                </div>
            <?php endif; ?>
        </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>