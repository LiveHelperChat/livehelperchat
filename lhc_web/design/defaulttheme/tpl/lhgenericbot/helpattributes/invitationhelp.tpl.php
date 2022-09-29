<?php if ($context == 'invitationtag') : ?>
    <p>Tag structure examples</p>
    <ul>
        <li><strong>error_deposit</strong> = simple tag example</li>
    </ul>
<p>If in embed code <i>__reset</i> suffix is appended invitation will be shown always independently was there any other invitation active or not.</p>
<pre>
function addTag(){
    $_LHC.eventListener.emitEvent('addTag',['error_deposit__reset']);
    window.$_LHC.eventListener.emitEvent('showWidget');
}
</pre>
<?php endif; ?>