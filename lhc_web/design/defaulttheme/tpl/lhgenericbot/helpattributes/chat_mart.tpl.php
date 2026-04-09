<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Describes the maximum time the agent takes to reply a message to the visitor.');?></p>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'For each participant, we calculate all valid reply times. MART is not an average, it is the single highest response time from that participant.');?></p>

<ul>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Formula (per participant): MART = max(that participant response times).');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'A response time starts when a visitor message appears.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'A response time ends when that same participant sends the next real operator message.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Only positive delays are used. If there is no valid reply for a visitor message, nothing is added.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'The first part of pending state is ignored: replies must happen after (pending time + wait time).');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If visitor message was before that start point, calculation starts from (pending time + wait time), not from visitor message time.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Included messages for response timing: visitor messages and normal operator messages.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Excluded from response timing: plain system messages that are not supported actions, meta actions (assign/transfer/change owner/change department/accept), and bot messages.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If participant has no valid response times, their MART stays 0.');?></li>
</ul>