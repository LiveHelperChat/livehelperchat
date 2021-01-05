<tr>
    <td colspan="2">

        <h6 class="font-weight-bold py-2"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></h6>

        <div class="row text-muted">
            <div class="col-6 pb-2">
                <?php if ( !empty($chat->uagent) ) : ?>
                    <i class="material-icons" title="<?php echo htmlspecialchars($chat->uagent)?>"><?php echo ($chat->device_type == 0 ? 'computer' : ($chat->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($chat->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($chat->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?>
                <?php endif;?>
            </div>
            <div class="col-6 pb-2" title="IP">
                <?php echo htmlspecialchars($chat->ip)?>
            </div>

            <?php if (!empty($chat->email)) : ?>
            <div class="col-6 pb-2">
                <span class="material-icons">email</span><a class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?>" href="mailto:<?php echo $chat->email?>"><?php echo $chat->email?></a>
            </div>
            <?php endif;?>

            <?php if (!empty($chat->phone)) : ?>
            <div class="col-6 pb-2">
                <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Phone')?>">phone</span><?php echo htmlspecialchars($chat->phone)?>
            </div>
            <?php endif;?>

            <?php if (($online_user = $chat->online_user) !== false) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_pre.tpl.php'));?>
                <?php if ($information_tab_online_user_info_enabled == true) : ?>
                    <div class="col-6 pb-2">
                        <a onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+<?php echo $online_user->id?>})"><span class="material-icons">info_outline</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Online profile')?></a>
                    </div>

                    <div class="col-6 pb-2">
                        <a onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/<?php echo $online_user->id?>/(tab)/chats'})"><span class="material-icons">info_outline</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Previous chats')?></a>
                    </div>


                <?php endif;?>
            <?php endif; ?>

            <?php if (!empty($chat->referrer)) : ?>
            <div class="col-12 pb-2">
                <div title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Page')?>"><span class="material-icons">link</span><?php echo $chat->referrer != '' ? '<a target="_blank" class="text-muted" rel="noopener" title="' . htmlspecialchars($chat->referrer) . '" href="' .htmlspecialchars($chat->referrer). '">'.htmlspecialchars($chat->referrer).'</a>' : ''?></div>
            </div>
            <?php endif;?>

            <?php if (!empty($chat->session_referrer)) : ?>
            <div class="col-12 pb-2">
                <div title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?>"><span class="material-icons">flight_land</span><?php echo $chat->session_referrer != '' ? '<a target="_blank" class="text-muted" rel="noopener" title="' . htmlspecialchars($chat->session_referrer) . '" href="' . htmlspecialchars($chat->session_referrer) . '">'.htmlspecialchars($chat->session_referrer).'</a>' : ''?></div>
            </div>
            <?php endif;?>

            <?php if ( !empty($chat->country_code) ) : ?>
            <div class="col-6 pb-2">
                <img src="<?php echo erLhcoreClassDesign::design('images/flags')?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php echo htmlspecialchars($chat->chat_locale)?>
            </div>
            <?php endif;?>

            <?php if ( !empty($chat->city) ) : ?>
            <div class="col-6 pb-2">
                <?php echo htmlspecialchars($chat->city);?>
            </div>
            <?php endif;?>

            <?php if ($canEditChat == true) : ?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/send_mail.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_contact.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/show_survey.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_user.tpl.php'));?>

            <?php endif; ?>

            <?php if ($canEditChat == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowblockusers')) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser.tpl.php'));?>
            <?php endif;?>

        </div>

    </td>
</tr>
