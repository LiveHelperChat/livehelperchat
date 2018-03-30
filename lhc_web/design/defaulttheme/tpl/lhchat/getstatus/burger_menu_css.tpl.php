<?php

$burgerMenu = "

ul." . $chatCSSPrefix . "-cf {
    font-size: 0;
    list-style: none;
    float:left!important;
}

ul." . $chatCSSPrefix . "-cf li {
    display: inline-block;
    position: relative;
    min-width: 100%;
}

ul." . $chatCSSPrefix . "-cf a {
    display: block;
    text-decoration: none;
}

ul." . $chatCSSPrefix . "-cf li ul a{
    background-color:#" . ($theme !== false ? $theme->header_background : '525252') .";
    padding:7px!important;
}

ul." . $chatCSSPrefix . "-cf > li > a{
    font-size:21px!important;
    color:#a6a6a6;
    text-align:center!important;
}

ul." . $chatCSSPrefix . "-cf li ul {
    left: -5px;
    position: absolute;
    top: 22px;
    visibility: hidden;
    z-index: 1;
}

ul." . $chatCSSPrefix . "-cf li:hover ul {
    top: 22px;
    visibility: visible;
}

ul." . $chatCSSPrefix . "-cf li ul a:hover {
    background: #". ($theme !== false ? $theme->need_help_hover_bg : '6b6b6b') . ";
}

";

echo str_replace(array("\n","\r"),"",$burgerMenu);

?>