<?php if ((int)erLhcoreClassModelChatConfig::fetch('show_language_switcher')->current_value == 1) : ?>
<a href="#" data-dropdown="drop1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Choose your language');?>" class="<?php if (isset($rightLanguage) && $rightLanguage == true) : ?>right <?php endif;?>button tiny secondary radius dropdown"><i class="icon-language"></i></a>
<?php 
$enabledLanguages = explode(',',erLhcoreClassModelChatConfig::fetch('show_languages')->current_value);
$langArray = array(
	'eng' => 'English',
    'lit' => 'Lietuviškai',
    'hrv' => 'Croatian',
    'esp' => 'Spanish',
    'por' => 'Portuguese',
    'nld' => 'Dutch',
    'ara' => 'Arabic',
    'ger' => 'German',
    'pol' => 'Polish',
    'rus' => 'Russian',
    'ita' => 'Italian',
    'fre' => 'Français',
    'chn' => 'Chinese',
    'cse' => 'Czech',
    'nor' => 'Norwegian',
    'tur' => 'Turkish',
    'vnm' => 'Vietnamese',
    'idn' => 'Indonesian',
    'sve' => 'Swedish',
    'per' => 'Persian',
    'ell' => 'Greek',
    'dnk' => 'Danish',
    'rou' => 'Romanian',
    'bgr' => 'Bulgarian',
    'tha' => 'Thai',
    'geo' => 'Georgian',
    'fin' => 'Finnish',
    'alb' => 'Albanian',
);
?>
<ul id="drop1" class="f-dropdown f-dropdown-lang">
	<?php foreach ($enabledLanguages as $siteAccess) : ?>
		<li><a onclick="return lhinst.switchLang($('#form-start-chat'),'<?php echo $siteAccess?>')" href="#"><?php echo $langArray[$siteAccess]?></a>
	<?php endforeach;?>   
</ul>
<?php endif;?>