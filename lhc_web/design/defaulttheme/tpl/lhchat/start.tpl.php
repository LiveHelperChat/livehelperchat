<script>
    window.initializeLHC = "lhc_init:"+JSON.stringify(<?php
        $params = array(
            'mode' => 'popup',
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

        if (isset($custom_fields)) {
            $params['CUSTOM_FIELDS'] = $custom_fields;
        }
        if (isset($jsVars)) {
            $params['jsVars'] = $jsVars;
        }

        echo json_encode($params); ?>);
</script>