<?php if ($context == 'invitationtag') : ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Tag structure examples');?></p>
    <ul>
        <li><strong>error_deposit</strong> = <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'simple tag example');?></li>
    </ul>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If in embed code');?> <i>__reset</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'suffix is appended invitation will be shown always independently was there any other invitation active or not.');?></p>
<pre>
function addTag(){
    $_LHC.eventListener.emitEvent('addTag',['error_deposit__reset']);
    window.$_LHC.eventListener.emitEvent('showWidget');
}
</pre>
<?php endif; ?>