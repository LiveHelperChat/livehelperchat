<?php if ($context == 'cannedreplacerules') : ?>
    <ul>
        <li><strong>{args.chat.referrer}</strong> `contains`. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Page where chat started');?></li>
        <li><strong>{args.chat.session_referrer}</strong> `contains`. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Referer from where visitor come to site.');?></li>
        <li><strong>{args.chat.chat_variables_array.&lt;variables&gt;}</strong> = <b>New</b></li>
        <li><strong>{args.chat.dep_id}</strong> = Department ID</li>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/cannedreplacerules_multiinclude.tpl.php'));?>
    </ul>
<?php endif; ?>