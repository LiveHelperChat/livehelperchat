<title><?php if (isset($Result['path'])) : ?>
<?php
$ReverseOrder = $Result['path'];
krsort($ReverseOrder);
foreach ($ReverseOrder as $pathItem) : ?><?php echo htmlspecialchars($pathItem['title']).' '?>&laquo;<?php echo ' ';endforeach;?>
<?php endif; ?>
<?php echo htmlspecialchars(erLhcoreClassModelChatConfig::fetch('application_name')->current_value)?></title>