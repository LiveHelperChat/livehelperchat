<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Describes the average time the agent takes to reply a message to the visitor, it considers the whole conversation.');?></p>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'For each participant, we collect all their valid reply times, add them together, and divide by how many replies they made. Result is rounded to full seconds.');?></p>

<ul>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Formula (per participant): AART = round(sum of that participant response times / number of that participant responses).');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'A response time starts when a visitor message appears.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Consecutive visitor messages do not reset response start point. While waiting for operator reply, only the first pending visitor start is used; later visitor messages do not replace it.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'A response time ends when that same participant sends the next real operator message.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Only positive delays are used. If there is no valid reply for a visitor message, nothing is added.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'The first part of pending state is ignored: replies must happen after (pending time + wait time).');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If visitor message was before that start point, calculation starts from (pending time + wait time), not from visitor message time.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Included messages for response timing: visitor messages and normal operator messages.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Excluded from response timing: plain system messages that are not supported actions, meta actions (assign/transfer/change owner/change department/accept), and bot messages.');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If participant has no valid response times, their AART stays 0.');?></li>
</ul>