<?php if ($leaveamessage === false && $online === false) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_not_available.tpl.php'));?>
<?php else : ?>
<script>
(function (){

    <?php if (isset($font_size) && $font_size > 0) : ?>
    if (!!window.localStorage && localStorage.setItem) try {
        localStorage.setItem(<?php echo json_encode($app_scope);?>+'_dfs',<?php echo $font_size?>);
    } catch (d) {
    }
    <?php endif; ?>

    var initParams = <?php
        $params = array(
            'mode' => $mode,
            'onlineStatus' => $online,
            'widgetStatus' => true,
            'isMobile' => $isMobile,
            'department' => $department,
            'captcha' => $captcha,
            'theme' => $theme,
            'domain_lhc' => $domain_lhc,
            'base_url' => erLhcoreClassDesign::baseurldirect(),
            'lang' => erLhcoreClassSystem::instance()->SiteAccess,
            'static_chat' => array(
                'id' => $id,
                'hash' => $hash,
                'vid' => $vid,
            ),
        );

        if ($sound == 1) {
            $params['toggleSound'] = true;
        }

        $params['staticJS']['chunk_js'] = erLhcoreClassDesign::design('js/widgetv2');
        $params['staticJS']['dir'] = erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language');
        $params['staticJS']['cl'] = erConfigClassLhConfig::getInstance()->getDirLanguage('content_language');

        if (isset($prefill)) {
            $params['attr_prefill'] = $prefill;
        }

        if (isset($inv) && $inv != '') {
            $params['proactive'] = array(
                'invitation' => $inv
            );
        }

        if (isset($survey) && $survey != '') {
            $params['survey'] = (int)$survey;
        }

        if (isset($priority) && $priority != '') {
            $params['priority'] = (int)$priority;
        }

        if (isset($operator) && is_numeric($operator)) {
            $params['operator'] = (int)$operator;
        }

        if (isset($bot) && is_numeric($bot)) {
            $params['bot_id'] = (int)$bot;
        }

        if (isset($trigger) && is_numeric($trigger)) {
            $params['trigger_id'] = (int)$trigger;
        }

        if (isset($custom_fields)) {
            $params['CUSTOM_FIELDS'] = $custom_fields;
        }

        if (isset($jsVars)) {
            $params['jsVars'] = $jsVars;
        }

        if (isset($prefill_admin)) {
            $params['attr_prefill_admin'] = $prefill_admin;
        }

        echo json_encode($params); ?>;

    var hash = window.location.hash;
    if (hash != '') {
        var chatParams = hash.replace('#/','').split('/');
        if (typeof chatParams[0] !== 'undefined' && !isNaN(chatParams[0]) && typeof chatParams[1] !== 'undefined' && chatParams[1].length > 20) {
            initParams.static_chat.id = parseInt(chatParams[0]);
            initParams.static_chat.hash = chatParams[1];
        }
    }

    <?php include(erLhcoreClassDesign::designtpl('lhchat/events_tracking.tpl.php'));?>

    window.initializeLHC = "lhc_init:"+JSON.stringify(initParams);
})();
</script>
<?php endif;?>