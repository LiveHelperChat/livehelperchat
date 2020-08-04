<?php

const RCUBE_CHARSET = 'UTF-8';

include 'lib/core/lhmailconv/html_parsers/rcube_func.php';

class erLhcoreClassMailconvHTMLParser {

    public static function getHTMLPreview($body) {

        $safe_mode = true;
        $part_no = 1;
        $container_id = 1;
        $container_attrib = 1;

        $body_args = array(
            'safe'         => $safe_mode,
            'plain'        => false,
            'css_prefix'   => 'v' . $part_no,
            'body_class'   => 'rcmBody',
            'container_id'     => $container_id,
            'container_attrib' => $container_attrib,
            'inline_html' => true,
            'css_prefix' => 'lhc-mail-',

            // Allow to show style or not
            'rcmail_washtml_callback' => false,

            // Do not show forms
            'skip_washer_form_callback' => false
        );

        $part = new stdClass();
        $part->ctype_secondary = 'html';
        $part->mime_id = 1;
        $part->replaces = [];

        // Parse the part content for display
        $body = rcmail_print_body($body, $part, $body_args);

        $body = str_replace(['<!-- html ignored -->','<!-- head ignored -->','<!-- meta ignored -->'],'',$body);

        return $body;
    }

}