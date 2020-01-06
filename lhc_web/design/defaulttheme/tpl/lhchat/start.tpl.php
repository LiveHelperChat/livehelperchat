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
            'base_url' => rtrim(erLhcoreClassDesign::baseurl(),"/"),
            'static_chat' => array(
                'id' => $id,
                'hash' => $hash,
                'vid' => $vid,
            ),
        );

        if (isset($prefill)) {
            $params['attr_prefill'] = $prefill;
        }

        if (isset($custom_fields)) {
            $params['CUSTOM_FIELDS'] = $custom_fields;
        }
        if (isset($jsVars)) {
            $params['jsVars'] = $jsVars;
        }

        echo json_encode($params); ?>);
</script>