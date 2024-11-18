<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Describes how long chat took before it was closed. Chat duration is based on time spend between messages.');?></p>

<ul>
    <li><?php echo \erLhcoreClassModelChatConfig::fetch('cduration_timeout_user')->current_value ?> minutes. How long an operator can wait before response is ignored for a visitor message.</li>
    <li><?php echo \erLhcoreClassModelChatConfig::fetch('cduration_timeout_operator')->current_value ?> minutes. How long a visitor can wait before response is ignored for an operator message.</li>
</ul>