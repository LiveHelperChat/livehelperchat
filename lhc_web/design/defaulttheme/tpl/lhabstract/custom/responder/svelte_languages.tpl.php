<script>
    <?php $responderFields = [];

    if (!isset($autoResponderOptions['hide_pending']) || $autoResponderOptions['hide_pending'] === false){
        $responderFields[] = [
            'name' => 'wait_message',
            'bind_name' => 'wait_message',
            'name_literal' => $fields['wait_message']['trans']
        ];
    }

    $responderFields[] = [
        'name' => 'multilanguage_message',
        'bind_name' => 'multilanguage_message',
        'name_literal' => $fields['multilanguage_message']['trans']
    ];

    if (!isset($autoResponderOptions['hide_operator_nick']) || $autoResponderOptions['hide_operator_nick'] === false) {
        $responderFields[] = [
            'name' => 'operator',
            'bind_name' => 'operator',
            'name_literal' => $fields['operator']['trans']
        ];
    }

    if (!isset($autoResponderOptions['hide_wait_message']) || $autoResponderOptions['hide_wait_message'] === false) {
        $responderFields[] = [
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Pending chat messaging'),
            'type' => 'header_block',
        ];
        $responderFields[] = [
            'name' => 'timeout_message',
            'bind_name' => 'timeout_message',
            'name_literal' => $fields['timeout_message']['trans']
        ];

        for ($i = 2; $i <= 5; $i++) {
            $responderFields[] = [
                'name' => 'timeout_message_' . $i,
                'bind_name' => 'timeout_message_'.$i,
                'name_literal' => $fields['timeout_message_' . $i]['trans']
            ];
        }
    }

    $responderFields[] = [
        'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor not replying messaging'),
        'type' => 'header_block',
    ];

    for ($i = 1; $i <= 5; $i++) {
        $responderFields[] = [
            'column' => 6,
            'name' => 'timeout_reply_message_' . $i,
            'bind_name' => 'timeout_reply_message_'.$i,
            'name_literal' => $fields['timeout_reply_message_' . $i]['trans']
        ];
    }

    $responderFields[] = [
        'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator not replying messaging'),
        'type' => 'header_block',
    ];

    for ($i = 1; $i <= 5; $i++) {
        $responderFields[] = [
            'column' => 6,
            'name' => 'timeout_op_trans_reply_message_' . $i,
            'bind_name' => 'timeout_op_trans_reply_message_'.$i,
            'name_literal' => $fields['timeout_reply_message_' . $i]['trans']
        ];
    }

    $responderFields[] = [
        'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','On-hold chat messaging'),
        'type' => 'header_block',
    ];

    $responderFields[] = [
        'name' => 'wait_timeout_hold',
        'bind_name' => 'wait_timeout_hold',
        'name_literal' => $fields['wait_timeout_hold']['trans']
    ];

    for ($i = 1; $i <= 5; $i++){
        $responderFields[] = [
            'column' => 6,
            'name' => 'timeout_hold_message_' . $i,
            'bind_name' => 'timeout_hold_message_'.$i,
            'name_literal' => $fields['timeout_hold_message_' . $i]['trans']
        ];
    }

    if (!isset($autoResponderOptions['hide_personal_closing']) || $autoResponderOptions['hide_personal_closing'] === false) {
        $responderFields[] = [
            'name' => $fields['close_message']['trans'],
            'type' => 'header_block',
        ];

        $responderFields[] = [
            'name' => 'close_message',
            'bind_name' => 'close_message'
        ];
    }

    ?>
    window.autoResponderFields = <?php echo json_encode($responderFields)?>;
</script>