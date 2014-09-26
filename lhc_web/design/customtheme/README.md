In this folder you can create your own overrides of the templates
==

* In order to find out which template is used in one or another window, enable debug output in settings/settings.ini.php:
  * debug_output => true
* Template/Image/CSS is overridden by creating the same path file in a custom theme. So if you see that used, the template path is:
 * defaulttheme/tpl/lhchat/chat.tpl.php
* So you have to create the same file in:
 * customtheme/tpl/lhchat/chat.tpl.php
* In this way you can override the original ones and create new templates.
