<?php
$translation = erTranslationClassLhTranslation::getInstance();
$modalHeaderTitle = $translation->getTranslation('abstract/message_protection', 'Blocked link');
$reasons = [
    'URL_NOT_IN_ALLOW_LIST' => $translation->getTranslation('abstract/message_protection', 'This URL is not in the allow list.'),
    'URL_IN_DENY_LIST' => $translation->getTranslation('abstract/message_protection', 'This URL is in the deny list.'),
    'URL_FORMAT' => $translation->getTranslation('abstract/message_protection', 'This URL has an invalid format.'),
    'URL_USER_INFO' => $translation->getTranslation('abstract/message_protection', 'This URL contains user information.'),
    'URL_BLOCKED_SCHEME' => $translation->getTranslation('abstract/message_protection', 'This URL uses a blocked scheme.'),
];
$explanation = isset($reasons[$reason]) ? $reasons[$reason] : $translation->getTranslation('abstract/message_protection', 'This link was blocked by a content protection rule.');
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php')); ?>
<p><?php echo htmlspecialchars($explanation, ENT_QUOTES, 'UTF-8'); ?></p>
<p><?php echo htmlspecialchars($translation->getTranslation('abstract/message_protection', 'Rule'), ENT_QUOTES, 'UTF-8'); ?>: <?php echo \LiveHelperChat\Helpers\BBCode\Mask::render($reason); ?></p>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php')); ?>