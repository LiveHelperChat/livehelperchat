<?php if ($leaveamessage === false && $online === false) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_not_available.tpl.php'));?>
<?php else : ?>
<script>
    window.initializeLHC = "lhc_init:"+JSON.stringify(<?php
        $params = array(
            'mode' => $mode,
            'onlineStatus' => $online,
            'widgetStatus' => true,
            'isMobile' => $isMobile,
            'department' => $department,
            'captcha' => $captcha,
            'theme' => $theme,
            'base_url' => erLhcoreClassDesign::baseurldirect(),
            'lang' => erLhcoreClassSystem::instance()->SiteAccess,
            'static_chat' => array(
                'id' => $id,
                'hash' => $hash,
                'vid' => $vid,
            ),
        );
        
        $params['staticJS']['chunk_js'] = erLhcoreClassDesign::design('js/widgetv2');
        
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

        if (isset($custom_fields)) {
            $params['CUSTOM_FIELDS'] = $custom_fields;
        }

        if (isset($jsVars)) {
            $params['jsVars'] = $jsVars;
        }

        if (isset($prefill_admin)) {
            $params['attr_prefill_admin'] = $prefill_admin;
        }

        echo json_encode($params); ?>);
</script>
<?php endif;?>