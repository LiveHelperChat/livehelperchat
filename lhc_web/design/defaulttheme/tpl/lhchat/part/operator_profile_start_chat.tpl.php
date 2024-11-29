<?php if (isset($bot) && isset($departament_id) && $departament_id > 0) : ?>
    <?php
        $user = $bot;
        $chat = new erLhcoreClassModelChat();
        $chat->dep_id = $departament_id;
    ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_bot.tpl.php')); ?>
<?php else : ?>
    <div class="operator-info d-flex">
        <div>
            <?php if ($theme !== false && $theme->operator_image_avatar !== false) : ?>

                <?php if ($theme->operator_image_url !== false) : ?>
                    <img width="48" height="48" src="<?php echo $theme->operator_image_url?>" alt="" />
                <?php else : ?>
                    <img width="48" height="48" src="<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($theme->bot_configuration_array['operator_avatar'])?>" alt="" />
                <?php endif; ?>

            <?php else : ?>
                <i class="icon-assistant material-icons">
                    <?php if (isset($react) && $react == true) : ?>&#xf10d;<?php else : ?>account_box<?php endif; ?>
                </i>
            <?php endif;?>
        </div>
        <div class="p-1 ps-2 w-100">

            <?php if (!isset($react)) : ?>
                <?php $rightLanguage = true;?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
            <?php endif; ?>

            <span class="fst-italic operator-profile-start-chat"><?php if ($theme !== false && $theme->intro_operator_text != '') : ?><?php echo $theme->intro_operator_text; ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Have a question? Ask us!');?><?php endif;?></span>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_start_chat_post.tpl.php'));?>
        </div>
    </div>
<?php endif; ?>


