<div class="modal-dialog modal-lg mx-4">
    <div class="modal-content">
        <div class="modal-header py-2 px-3">
            <h5 class="modal-title" id="myModalLabel">
                <span class="material-icons">&#xf11e;</span>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your language')?>
            </h5>
            <button type="button" id="react-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-0" >
                <?php
                $enabledLanguages = explode(',',erLhcoreClassModelChatConfig::fetch('show_languages')->current_value);
                $langArray = array(
                    'eng' => 'English',
                    'lit' => 'Lietuviškai',
                    'hrv' => 'Croatian',
                    'esp' => 'Spanish',
                    'por' => 'Portuguese',
                    'nld' => 'Dutch',
                    'ara' => 'Arabic',
                    'ger' => 'German',
                    'pol' => 'Polish',
                    'rus' => 'Russian',
                    'ita' => 'Italian',
                    'fre' => 'Français',
                    'chn' => 'Chinese',
                    'cse' => 'Czech',
                    'nor' => 'Norwegian',
                    'tur' => 'Turkish',
                    'vnm' => 'Vietnamese',
                    'idn' => 'Indonesian',
                    'sve' => 'Swedish',
                    'per' => 'Persian',
                    'ell' => 'Greek',
                    'dnk' => 'Danish',
                    'rou' => 'Romanian',
                    'bgr' => 'Bulgarian',
                    'tha' => 'Thai',
                    'geo' => 'Georgian',
                    'fin' => 'Finnish',
                    'alb' => 'Albanian',
                );
                ?>
                <div class="row">
                    <?php foreach ($enabledLanguages as $siteAccess) : ?>
                        <div class="col-4">
                            <a class="badge fs13 bg-secondary m-1 action-image<?php if (erLhcoreClassSystem::instance()->SiteAccess == $siteAccess) : ?> fw-bold<?php endif; ?>" linkaction="true" data-action="setLanguage" data-action-arg="<?php echo $siteAccess?>"><?php echo $langArray[$siteAccess]?></a>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>